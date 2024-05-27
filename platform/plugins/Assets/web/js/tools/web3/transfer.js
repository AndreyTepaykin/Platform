(function (window, Q, $, undefined) {

var Assets = Q.Assets;
var Users = Q.Users;

/**
 * @module Assets
 */

/**
 * Allows a user to transfer tokens to someone else
 * @class Assets/web3/transfer
 * @constructor
 * @param {Object} options Override various options for this tool
 * @param {String} [options.recipientUserId] - id of user to whom the tokens should be sent
 * @param {Q.Event} [options.onSubmitted] - when signed transaction is submitted to the mempool to be mined
 * @param {Boolean} [options.withHistory] - if true ad a Assets/history tool to the bottom
 */

Q.Tool.define("Assets/web3/transfer", function (options) {
    var tool = this;
    var state = this.state;

    tool[state.action]();
},

{ // default options here
    action: "send",
    recipientUserId: null,
    tokenInfo: null,
    withHistory: false,
    onSubmitted: new Q.Event()
},

{ // methods go here
    refresh: function () {
        var tool = this;
        var state = this.state;

    },
    send: function () {
        var tool = this;
        var state = this.state;
        var $toolElement = $(tool.element);
        var _setHistoryTool = function (userId) {
            // TODO: show history of web3 transactions to a wallet address
            // which may not necessarily correspond to a user
            var $history = $(".Assets_transfer_history", tool.element);
            if ($history.length) {
                Q.Tool.remove($history[0], true, false);
                $history.tool("Assets/history", {
                    type: "credits",
                    withUserId: userId
                }).activate();
            }
        };

        Q.Template.render("Assets/web3/transfer/send", {
            recipientUserId: state.recipientUserId,
            tokenInfo: state.tokenInfo,
            withHistory: state.withHistory
        }, {
            activateInContainer: tool.element,
            onActivate: function (err, html) {
                var userSelected = null;
                var $send = $("button[name=send]", tool.element);
                var $userSelected = $(".Assets_transfer_userSelected", tool.element);
                tool.assetsWeb3BalanceTool = null;
    
                if (Q.isEmpty(state.recipientUserId)) {
                    var addressTool = tool.child('Users_web3_address');
                    addressTool.state.onAddress.set(function (wallet, userId, avatar) {
                        if (state.tokenInfo) {
                            return;
                        }
                        userSelected = {
                            userId: userId,
                            wallet: wallet,
                            walletError: null
                        };
                        $toolElement.addClass("Q_disabled");
                        $(".Assets_transfer_balance", tool.element).tool("Assets/web3/balance", {
                            skipWeb3: !wallet
                        }).activate(function () {
                            this.state.onChainChange.add(function () {
                                $toolElement.addClass("Q_disabled");
                            }, tool);
                            this.state.onChainChanged.add(function () {
                                $toolElement.removeClass("Q_disabled");
                            }, tool);
                            tool.assetsWeb3BalanceTool = this;
                        });

                        _setHistoryTool(userId);
                    }, tool);
    
                    // $(".Assets_transfer_userChooser", tool.element).tool("Streams/userChooser").activate(function () {
                    //     this.state.onChoose.set(function (userId, avatar) {
                    //         _getSelectedUser(userId);
                    //     }, tool);
                    // });
    
                    /*$(".Assets_transfer_usersList", tool.element).tool("Streams/people", {
                        avatar: {
                            short: true,
                            icon: '50'
                        }
                    }).activate(function () {
                        this.state.onChoose.set(function () {
                            //TODO: when Streams/people tool ready, move onChoose event handler here from above
                        }, tool);
                    });*/
                } else {
                    _getSelectedUser(state.recipientUserId);
                    _setHistoryTool(state.recipientUserId);
                }

                var $amount = $("input[name=amount]", tool.element);
                $send.on(Q.Pointer.fastclick, function () {
                    var $this = $(this);
                    var tokenInfo = state.tokenInfo;
                    if (Q.isEmpty(tokenInfo)) {
                        tokenInfo = tool.assetsWeb3BalanceTool.getValue();
                    }
                    var amount = parseFloat($amount.val());
                    if (Q.isEmpty(amount)) {
                        return Q.alert(tool.text.errors.AmountInvalid);
                    }
                    var _transactionSuccess = function () {
                        $this.removeClass("Q_working");
                        Q.Dialogs.pop();
                        Q.alert(tool.text.transfer.TransactionSuccess);
                    };
    
                    $this.addClass("Q_working");
    
                    if (tokenInfo.tokenName === "credits") {
                        return Assets.Credits.pay({
                            amount: amount,
                            currency: "credits",
                            userId: userSelected.userId,
                            onSuccess: function () {
                                _transactionSuccess();
                            },
                            onFailure: function (err) {
                                Q.Dialogs.pop();
                                console.warn(err);
                                $this.removeClass("Q_working");
                            }
                        });
                    }
    
                    if (!amount || amount > tokenInfo.tokenAmount) {
                        $this.removeClass("Q_working");
                        return Q.alert(tool.text.errors.AmountInvalid);
                    }
    
                    var walletSelected = $("input[name=wallet]", tool.element).val() || Q.getObject("wallet", userSelected);
    
                    if (Q.isEmpty(walletSelected)) {
                        $this.removeClass("Q_working");
                        return Q.alert(userSelected.walletError || tool.text.errors.NoRecipientSelected);
                    } else if (!ethers.utils.isAddress(walletSelected)) {
                        $this.removeClass("Q_working");
                        return Q.alert(tool.text.errors.WalletInvalid);
                    }
    
                    var parsedAmount = ethers.utils.parseUnits(String(amount), tokenInfo.decimals);
    
                    Users.Web3.withChain(tokenInfo.chainId, function () {
                        if (tokenInfo.tokenAddress === Q.Users.Web3.zeroAddress) {
                            Users.Web3.transaction(walletSelected, amount, function (err, transactionRequest, transactionReceipt) {
                                Q.handle(state.onSubmitted, tool, [err, transactionRequest, transactionReceipt]);
    
                                if (err) {
                                    Q.alert(Users.Web3.parseMetamaskError(err));
                                    return $this.removeClass("Q_working");
                                }
    
                                _transactionSuccess();
                            }, {
                                wait: 1,
                                chainId: tokenInfo.chainId
                            });
                            return;
                        }
    
                        Users.Web3.getContract("Assets/templates/ERC20", {
                            chainId: tokenInfo.chainId,
                            contractAddress: tokenInfo.tokenAddress,
                            readOnly: false
                        }, function (err, contract) {
                            if (err) {
                                //Q.alert(Users.Web3.parseMetamaskError(err, [contract]));
                                return $this.removeClass("Q_working");
                            }
    
                            contract.on("Transfer", function _assets_web3_transfer_listener (from, to, value) {
                                if (walletSelected.toLowerCase() !== to.toLowerCase()) {
                                    return;
                                }
    
                                _transactionSuccess();
                                contract.off(_assets_web3_transfer_listener);
                            });
    
                            Users.Web3.withChain(tokenInfo.chainId, function () {
                                contract.transfer(walletSelected, parsedAmount).then(function (info) {
                                    Q.handle(state.onSubmitted, tool, [null, info]);
                                }, function (err) {
                                    Q.alert(Users.Web3.parseMetamaskError(err, [contract]));
                                    $this.removeClass("Q_working");
                                });
                            });
                        });
                    });
                });

                function _getSelectedUser (userId, xid) {    
                    Q.Streams.get(userId, "Streams/user/xid/web3", function (err) {
                        if (err) {
                            return;
                        }
    
                        var wallet, walletError, ethersError;
                        if (!window.ethers) {
                            ethersError = true;
                        } else if (!this.testReadLevel("content")) {
                            walletError = tool.text.errors.NotEnoughPermissionsWallet;
                        } else {
                            wallet = this.fields.content;
                            if (!wallet) {
                                walletError = tool.text.errors.ThisUserHaveNoWallet;
                            } else if (!ethers.utils.isAddress(wallet)) {
                                walletError = tool.text.errors.TheWalletOfThisUserInvalid;
                            }
                        }
    
                        if (!state.tokenInfo) {
                            $toolElement.addClass("Q_disabled");
                            $(".Assets_transfer_balance", tool.element).tool("Assets/web3/balance", {
                                skipWeb3: ethersError || !(wallet && !walletError)
                            }).activate(function () {
                                this.state.onChainChange.add(function () {
                                    $toolElement.addClass("Q_disabled");
                                }, tool);
                                this.state.onChainChanged.add(function () {
                                    $toolElement.removeClass("Q_disabled");
                                }, tool);
                                tool.assetsWeb3BalanceTool = this;
                            });
                        }
    
                        userSelected = null;
                        $(".Users_avatar_tool", $userSelected).each(function () {
                            Q.Tool.remove(this, true, true);
                        });
    
                        $("<div>").appendTo($userSelected).tool("Users/avatar", {
                            userId: userId,
                            icon: 50,
                            contents: true,
                            editable: false
                        }).activate();
    
                        userSelected = {
                            userId: userId,
                            wallet: this.fields.content,
                            walletError: walletError
                        };
    
                        $send.removeClass("Q_disabled");
                    });
                }
            }
        });
    }
});

Q.Template.set("Assets/web3/transfer/send",
    '{{#if recipientUserId}}{{else}}' +
        '{{{tool "Users/web3/address" ""}}}' +
    '{{/if}}' +
    '{{#if tokenInfo}}{{else}}' +
        '<div class="Assets_transfer_balance"></div>' +
    '{{/if}}' +
    '<div class="Assets_transfer_send">' +
        '<input name="amount" placeholder="{{payment.EnterAmount}}" />' +
        '<button class="Q_button" name="send">{{payment.Send}}</button>' +
    '</div>' +
    '{{#if withHistory}}' +
        '<div class="Assets_transfer_history"></div>' +
    '{{/if}}',
    { text: ['Assets/content'] }
);
})(window, Q, Q.jQuery);