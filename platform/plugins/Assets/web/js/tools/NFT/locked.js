(function (window, Q, $, undefined) {

    /**
     * YUIDoc description goes here
     * @class Assets NFT locked
     * @constructor
     * @param {Object} [options] Override various options for this tool
     * @param {String} [options.abiPath] ABI path for LockedHook contract
     * @param {String} [options.NFTAddress] NFTAddress address
     * @param {String} [options.abiNFT] ABI path for NFT contract
     * @param {String} [options.tokenId] tokenId(optional) if specify then tool will work in single mode. remove input with tokenId from popups. and hided btn lock/unlock if can not be proceeded
     * @param {Object} [seriesIdSource] Datasource for getting seriesId. it can be series number or sales address where script try to get series id from public state
     * @param {String} [options.seriesId] series ID
     * @param {String} [options.salesAddress] address of NFTsales contract
     * @param {String} [options.abiNFTSales] ABI path for NFTsales contract

     */
Q.Tool.define("Assets/NFT/locked", function (options) {
        var tool = this;
        var state = tool.state;
        var $toolElement = $(this.element);
        tool.NFTpreview = Q.Tool.from($toolElement.closest(".Assets_NFT_preview_tool")[0], "Assets/NFT/preview");

        if (Q.isEmpty(state.NFTAddress)) {
            $toolElement.remove();
            return console.warn("Assets/NFT/locked", "NFTAddress required!");
        }

        Promise.all([tool.nftContractPromise(), tool.lockedContractPromise()])
        .then(function (_ref) {
            var nftContract = _ref[0];
            var lockedContract = _ref[1];

            var seriesId = tool.getSeriesId();
            return nftContract.getHookList(seriesId).then(function (allHooksArr) {
                return [
                    allHooksArr.map(c => c.toLowerCase())
                        .indexOf(lockedContract.address) >= 0,
                    nftContract,
                    lockedContract,
                    seriesId
                ];
            });
        }).then(function ([b, nftContract, lockedContract, seriesId]) {
            if (!b) {
                console.group("Assets/NFT/locked Warn");
                console.log("locked contract does not setup on NFT as a hook on this seriesId");
                console.log("nftContract=", nftContract.address);
                console.log("lockedContract=", lockedContract.address);
                console.log("seriesId=", seriesId);
                console.groupEnd();
                $toolElement.remove();
                return;
            }

            tool.refresh();
        });

        Q.Users.Web3.onAccountsChanged.set(function () {
            tool.refresh();
        }, tool);
    },

    { // default options here
        abiPath: "Assets/templates/R1/NFT/locked",
        //lockedAddress: '',
        NFTAddress: '',
        abiNFT: "Assets/templates/R1/NFT/contract",
        tokenId: null,
        seriesIdSource: {
            seriesId: null,
            salesAddress: '',
            abiNFTSales: "Assets/templates/R1/NFT/sales/contract"
        },
        onMove: new Q.Event() // an event that the tool might trigger
    },

    { // methods go here
        getSeriesId: function () {
            return Q.Assets.NFT.seriesIdFromTokenId(this.state.tokenId);
        },
        nftContractPromise: function () {
            var state = this.state;
            return Q.Users.Web3.getContract(state.abiNFT, state.NFTAddress);
        },
        lockedContractPromise: function () {
            return Q.Users.Web3.getFactory(this.state.abiPath);
        },
        /**
         * Refreshes the appearance of the tool completely
         * @method getMyStream
         * @param {Function} callback receives arguments (err) with this = stream
         */
        refresh: function () {
            var tool = this;
            var state = tool.state;
            var $toolElement = $(this.element);

            // if user login then
            Q.Template.render("Assets/NFT/locked", {}, function (err, html) {
                Q.replace(tool.element, html);
                Q.activate(tool.element);

                if (!Q.isEmpty(state.tokenId)) {
                    // check current
                    Promise.all([tool.nftContractPromise(), tool.lockedContractPromise()]).then(function (_ref) {
                        var nftContract = _ref[0];
                        var lockedContract = _ref[1];

                        tool.lockedContractPromise().then(function (lockedContract) {
                            return lockedContract.isLocked(state.NFTAddress, state.tokenId);
                        }).then(function ([locked, custodian]) {
                            $(tool.NFTpreview.element).attr("data-locked", !!locked);
                            $toolElement.attr("data-locked", !!locked);
                            $toolElement.attr("data-custodian", custodian.toLowerCase() === Q.Users.Web3.getSelectedXid().toLowerCase());
                            nftContract.ownerOf(state.tokenId).then(function (owner) {
                                $toolElement.attr("data-owner", owner.toLowerCase() === Q.Users.Web3.getSelectedXid().toLowerCase());
                            });

                            $('.Assets_NFT_locked_locked', tool.element).on(Q.Pointer.fastclick, function () {
                                Q.Dialogs.push({
                                    title: tool.text.NFT.locked.CustodianAddress,
                                    content: '<div class="Q_messagebox Q_big_prompt"><p>' + custodian + '</p></div>',
                                    className: 'Q_alert',
                                    onActivate: function (dialog) {
                                        $(".Q_messagebox", dialog).tool('Q/textfill').activate();
                                    },
                                    fullscreen: false,
                                    hidePrevious: true
                                });
                            });
                        });
                    });
                }

                $('.Assets_NFT_locked_lockBtn', tool.element).on(Q.Pointer.fastclick, function () {
                    var state = tool.state;
                    Q.Dialogs.push({
                        title: tool.text.NFT.locked.Lock,
                        className: "Assets_NFT_locked_lock",
                        template: {
                            name: 'Assets/NFT/lock',
                            fields: {
                                noTokenId: !state.tokenId
                            }
                        },
                        onActivate: function (dialog) {
                            $(".Assets_NFT_locked_dialogLock", dialog).on(Q.Pointer.fastclick, function () {
                                $(this).addClass('Q_loading');
                                var tokenId = $(dialog).find("[name='tokenId']").val() || state.tokenId;
                                var custodian = $(dialog).find("[name='custodian']").val();
                                if (!tokenId) {
                                    Q.Dialogs.pop();
                                    return Q.alert(tool.text.NFT.locked.errors.invalidTokenId);
                                }
                                if (!custodian) {
                                    Q.Dialogs.pop();
                                    return Q.alert(tool.text.NFT.locked.errors.invalidCustodian);
                                }

                                return Promise.all([tool.nftContractPromise(), tool.lockedContractPromise()]).then(function (_ref) {
                                    var nftContract = _ref[0];
                                    var lockedContract = _ref[1];

                                    return nftContract.ownerOf(tokenId).then(function (owner) {
                                        if (owner.toLowerCase() != Q.Users.Web3.getSelectedXid().toLowerCase()) {
                                            throw new Error('Sender is not an owner for this tokenId');
                                        }
                                        return lockedContract.lock(nftContract.address, tokenId, custodian);
                                    }).then(function (txResponce) {
                                        txResponce.wait().then(function () {
                                            Q.Dialogs.pop();
                                            Q.Notices.add({
                                                content: tool.text.NFT.locked.TokenWasLocked.interpolate({"title": $(".Assets_NFT_title", tool.NFTpreview.element).html()}),
                                                timeout: 5
                                            });

                                            tool.refresh();
                                        }, function (err) {
                                            Q.Dialogs.pop();
                                            Q.handle(null, null, [err.reason]);
                                        });
                                    }).catch(function (err) {
                                        Q.Dialogs.pop();
                                        Q.Notices.add({
                                            content: Q.Users.Web3.parseMetamaskError(err, [nftContract, lockedContract]),
                                            timeout: 5
                                        });
                                        //Q.Dialogs.pop();
                                    })
                                });
                            });
                        }
                    });
                });

                $('.Assets_NFT_locked_unlockBtn', tool.element).on(Q.Pointer.fastclick, function () {
                    var $this = $(this);
                    var _contractUnlock = function (tokenId) {
                        Promise.all([tool.nftContractPromise(), tool.lockedContractPromise()]).then(function (_ref) {
                            var nftContract = _ref[0];
                            var lockedContract = _ref[1];
                            return lockedContract.unlock(nftContract.address, tokenId).then(function (txResponce) {
                                txResponce.wait().then(function () {
                                    Q.Dialogs.pop();
                                    $this.removeClass("Q_loading");
                                    Q.Notices.add({
                                        content: tool.text.NFT.locked.TokenWasUnlocked.interpolate({"title": $(".Assets_NFT_title", tool.NFTpreview.element).html()}),
                                        timeout: 5
                                    });
                                    tool.refresh();
                                }, function (err) {
                                    Q.Dialogs.pop();
                                    $this.removeClass("Q_loading");
                                    Q.handle(null, null, [err.reason]);
                                });
                            }).catch(function (err) {
                                Q.Dialogs.pop();
                                $this.removeClass("Q_loading");
                                Q.Notices.add({
                                    content: Q.grabMetamaskError(err, [nftContract, lockedContract]),
                                    timeout: 5
                                });
                            });
                        });
                    };

                    if (state.tokenId) {
                        $this.addClass("Q_loading");
                        return _contractUnlock(state.tokenId);
                    }

                    Q.Dialogs.push({
                        title: tool.text.NFT.locked.Unlock,
                        className: "Assets_NFT_locked_unlock",
                        template: {
                            name: 'Assets/NFT/unlock'
                        },
                        onActivate: function (dialog) {
                            $(".Assets_NFT_locked_dialogUnlock", dialog).on(Q.Pointer.fastclick, function () {
                                $(this).addClass('Q_loading');
                                var tokenId = $(dialog).find("[name='tokenId']").val();
                                if (!tokenId) {
                                    Q.Dialogs.pop();
                                    return Q.alert(tool.text.NFT.locked.errors.invalidTokenId);
                                }

                                _contractUnlock(tokenId);
                            });
                        }
                    });
                });
            });
        }
    });

Q.Template.set("Assets/NFT/locked",
`<div>
        <div class="Assets_NFT_sales_lock_сontainer">
            <button class="Assets_NFT_locked_lockBtn Q_button">{{NFT.locked.Lock}}</button>
            <button class="Assets_NFT_locked_unlockBtn Q_button">{{NFT.locked.Unlock}}</button>
            <a class="Assets_NFT_locked_locked">{{NFT.locked.Locked}}</a>
        </div>
</div>`,
    {text: ["Assets/content"]}
);

Q.Template.set("Assets/NFT/lock",
`<div class="Assets_NFT_locked_form">
        {{#if noTokenId}} 
            <div class="form-group">
                <label>{{NFT.locked.form.labels.tokenId}}</label>
                <input name="tokenId" type="text" class="form-control" placeholder="{{NFT.locked.placeholders.tokenId}}">
                <small class="form-text text-muted">{{NFT.locked.form.small.tokenId}}</small>
            </div>
        {{/if}} 
        <div class="form-group">
            <label>{{NFT.locked.form.labels.custodian}}</label>
            <input name="custodian" type="text" class="form-control" placeholder="{{NFT.locked.placeholders.custodian}}">
            <small class="form-text text-muted">{{NFT.locked.form.small.custodian}}</small>
        </div>
        <button class="Assets_NFT_locked_dialogLock Q_button">{{NFT.locked.Lock}}</button>
    </div>`, {text: ["Assets/content"]});

Q.Template.set("Assets/NFT/unlock",
`<div class="Assets_NFT_locked_form">
            <div class="form-group">
                <label>{{NFT.locked.form.labels.tokenId}}</label>
                <input name="tokenId" type="text" class="form-control" placeholder="{{NFT.locked.placeholders.tokenId}}">
                <small class="form-text text-muted">{{NFT.locked.form.small.tokenId}}</small>
            </div>
            <button class="Assets_NFT_locked_dialogUnlock Q_button">{{NFT.locked.Unlock}}</button>
        </div>`, {text: ["Assets/content"]});
})(window, Q, Q.jQuery);