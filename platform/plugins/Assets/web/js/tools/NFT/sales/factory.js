if (Q.isEmpty(Q["isAddress"])) {
    Q.isAddress = function _Q_isAddress(address) {
        // https://github.com/ethereum/go-ethereum/blob/aa9fff3e68b1def0a9a22009c233150bf9ba481f/jsre/ethereum_js.go#L2295-L2329
        if (!/^(0x)?[0-9a-f]{40}$/i.test(address)) {
            // check if it has the basic requirements of an address
            return false;
        } else if (/^(0x)?[0-9a-f]{40}$/.test(address) || /^(0x)?[0-9A-F]{40}$/.test(address)) {
            // If it's all small caps or all all caps, return true
            return true;
        } else {
            // Otherwise check each case
//            address = address.replace('0x','');
//            var addressHash = Web3.utils.sha3(address.toLowerCase());
//            for (var i = 0; i < 40; i++ ) {
//                // the nth letter should be uppercase if the nth digit of casemap is 1
//                if ((parseInt(addressHash[i], 16) > 7 && address[i].toUpperCase() !== address[i]) || (parseInt(addressHash[i], 16) <= 7 && address[i].toLowerCase() !== address[i])) {
//                    return false;
//                }
//            }
            return true;
        }
        
    }
}

if (Q.isEmpty(Q["validate"])) {
    Q.validate = function _Q_validate(address) {
    
    }
    Q.validate.notEmpty = function _Q_validate_notEmpty(input) {
        return !Q.isEmpty(input)
    }
    Q.validate.integer = function _Q_validate_integer(input) {
        return Q.isInteger(input)
    }
    Q.validate.address = function _Q_validate_address(input) {
        return Q.isAddress(input)
    }
}
(function (window, Q, $, undefined) {
	
/**
 * @module TokenSociety
 */
	
/**
 * YUIDoc description goes here
 * @class TokenSociety cool
 * @constructor
 * @param {Object} [options] Override various options for this tool
 *  @param {String} [options.fields] array of values by default. 
 *  @param {Q.Event} [options.onMove] Event that fires after a move
 */

Q.Tool.define("Assets/NFT/sales/factory", function (options) {
	var tool = this;
	var state = tool.state;
        
        var defaultsValidate = {
            notEmpty: "<b>%key%</b> is not be empty", 
            integer: "<b>%key%</b> is not a number", 
            address: "<b>%key%</b> invalid"
        };
        
        // fill missed attr fields
        for (var i in state.fields) {
            
            if (typeof(state.fields[i]) === "string") {
                state.fields[i] = {
                    value: state.fields[i],
                    hide: false
                }
            } else if (typeof(state.fields[i]) === "object") {
                let arr;
                if (Q.isEmpty(state.fields[i]["value"])) {
                    state.fields[i]["value"] = "";
                }
                if (Q.isEmpty(state.fields[i]["hide"])) {
                    state.fields[i]["hide"] = false;
                }
                
                if (Q.isEmpty(state.fields[i]["validate"])) {
                    state.fields[i]["validate"] = {};
                } else if (Array.isArray(state.fields[i]["validate"])) {
                    
                    arr = {};
                    for (var j in state.fields[i]["validate"]) {
                        let k = state.fields[i]["validate"][j];
                        if (Q.isEmpty(defaultsValidate[k])) {
                            console.warn(`validate expr "${k}" have not supported yet`);
                        } else {
                            arr[k] = defaultsValidate[k];
                        }
                    }
                    state.fields[i]["validate"] = Object.assign({}, arr);
                    
                } else if (typeof(state.fields[i]["validate"]) === "object") {
                    for (var j in state.fields[i]["validate"]) {
                        if (Q.isEmpty(defaultsValidate[j])) {
                            console.warn(`validate expr "${j}" have not supported yet`);
                        } else {
                            state.fields[i]["validate"][j] = state.fields[i]["validate"][j];
                        }
                    }
                }
            }
        }
        
	var p = Q.pipe(['stylesheet', 'text'], function (params, subjects) {
		tool.text = params.text[1];
		tool.refresh();
	});
        
	Q.addStylesheet("{{Assets}}/css/tools/NFT/sales/factory.css", p.fill('stylesheet'), { slotName: 'Assets' });
	Q.Text.get('Assets/content', p.fill('text'));
},

{ // default options here
    fields: {
        // key validate is optional
        // value can be :
        // - plain array
        //  validate: ["isEmpty", "isInteger", ...] and try to call Q methods: Q.isEmpty, Q.isInteger ...
        // - object  like {key => errormessage}
        //  validate: {"isEmpty": "err msg here to key %key%, "isInteger": "invalid key %key%, ...} and try to call Q methods: Q.isEmpty, Q.isInteger ...
        NFTContract: {value: "", hide: false, validate: ["notEmpty", "address"]},
        seriesId: {value: "", hide: false, validate: ["notEmpty", "integer"]},
        owner: {value: "", hide: false, validate: ["notEmpty", "address"]},
        currency: {value: "", hide: false, validate: ["notEmpty", "address"]},
        price: {value: "", hide: false, validate: ["notEmpty"]},
        beneficiary: {value: "", hide: false, validate: ["notEmpty", "address"]},
        autoindex: {value: "", hide: false, validate: ["notEmpty", "integer"]},
        duration: {value: "", hide: false, validate: ["notEmpty", "integer"]},
        rateInterval: {value: "", hide: false, validate: ["notEmpty", "integer"]},
        rateAmount: {value: "", hide: false, validate: ["notEmpty", "integer"]}
    },
    onMove: new Q.Event() // an event that the tool might trigger
},

{ // methods go here
    whitelistByNFT: function(NFTContract, callback){
        let contract;
        Q.Users.Web3.getFactory('Assets/templates/R1/NFT/sales/factory')
        .then(function(_contract){
            contract = _contract;
            return  contract.whitelistByNFTContract(NFTContract).then(function(res){return res});
        }).then(function (instancesList) {
            Q.handle(callback, null, [null, {list: instancesList}, contract])
        }).catch(function (err) {
            Q.handle(callback, null, [err.reason || err]);
        })

    },
    _whitelistPush: function(item){
        var tool = this;
        let obj = $(tool.element).find(".Assets_NFT_sales_factory_instancesTableList");
        if (obj.find('tr.Assets_NFT_sales_factory_item').length == 0) {
            obj.find('tr').hide();    // all defaults  like "there are no data  etc"
        }
        obj.prepend(`<tr class="Assets_NFT_sales_factory_item"><td><a href="/test2/${item}">${item}</a></td></tr>`);
    },
    _whitelistRefresh: function(){
        var tool = this;
        let obj = $(tool.element).find(".Assets_NFT_sales_factory_instancesTableList");
        obj.find('tr').hide();
        obj.find('tr.Assets_NFT_sales_factory_loading').show();
        tool.whitelistByNFT(TokenSociety.NFT.contract.address, function(err, data){
            obj.find('tr.Assets_NFT_sales_factory_loading').hide();    
            obj.find('tr').not('.Assets_NFT_sales_factory_loading').remove();    
            if (!data || Q.isEmpty(data.list)) {
                obj.append(`<tr><td>There are no instances</td></tr>`);
            } else {
                for (var i in data.list) {
                    obj.prepend(`<tr class="Assets_NFT_sales_factory_item"><td><a href="/test2/${data.list[i]}">${data.list[i]}</a></td></tr>`);
                }
            }
        });
    },
    /**
     * @notice create NFTSales instance
     * @param NFTContract NFTcontract's address that allows to mintAndDistribute for this factory
     * @param seriesId series ID in which tokens will be minted
     * @param owner owner's adddress for newly created NFTSales contract
     * @param currency currency for every sale NFT token
     * @param price price amount for every sale NFT token
     * @param beneficiary address where which receive funds after sale
     * @param autoindex from what index contract will start autoincrement from each series(if owner doesnot set before) 
     * @param duration locked time when NFT will be locked after sale
     * @param rateInterval interval in which contract should sell not more than `rateAmount` tokens
     * @param rateAmount amount of tokens that can be minted in each `rateInterval`
     * @return instance address of created instance `NFTSales`
     */
    produce: function(
        NFTContract, //address 
        seriesId, //uint256         
        owner, //address 
        currency, //address 
        price, //uint256 
        beneficiary, //address 
        autoindex, //uint192 
        duration, //uint64 
        rateInterval, //uint32 
        rateAmount, //uint16 
        callback
    ) {
        var tool = this;
        var state = this.state;
            
        return Q.Users.Web3.getFactory('Assets/templates/R1/NFT/sales/factory')
        .then(function (contract) {
            return contract.produce(
                NFTContract, 
                seriesId, 
                owner, 
                currency, 
                price, 
                beneficiary, 
                autoindex, 
                duration, 
                rateInterval, 
                rateAmount
            ).then(function(txResponse){
                txResponse.wait().then(function(receipt){
                    let event = receipt.events.find(event => event.event === 'InstanceCreated');
                    [instance] = event.args;
                    Q.Notices.add({
                        content: `Instance "${instance}" was created successfully`,
                        timeout: 5
                    });
                    tool._whitelistPush(instance);
                    Q.handle(callback, null, [null, instance]);
                }, function(err){
                    console.log("err::txResponce.wait()");
                    Q.handle(callback, null, [err.reason || err]);
                });
            });
        }).catch(function (err) {
            console.warn(err);
            Q.handle(callback, null, [err.reason || err]);
        });
    },
    /**
     * Refreshes the appearance of the tool completely
     * @method getMyStream
     * @param {Function} callback receives arguments (err) with this = stream
     */
    refresh: function () {

        var tool = this;
        var state = tool.state;

        // if user login then 
        Q.Template.render(
            "Assets/NFT/sales/factory", 
            {
                fields:state.fields,
                chainId: Q.Assets.NFT.defaultChain.chainId
            },
            function(err, html){

                tool.element.innerHTML = html;
                Q.activate(tool.element, function(){
                    $(tool.element).find("select").addClass("form-control");
                });

                tool.currency = null;
                
                $('.Assets_NFT_sales_factory_produce', tool.element).on(Q.Pointer.fastclick, function(){

                    
                    let objToolElement = $(tool.element);
                    // clone state fields
                    let fields = Object.assign({}, state.fields);
                    //collect form
                    for (let key in fields) {
                        // get field values
                        fields[key].userValue = objToolElement.find(`[name='${key}']`).val();
                        // use default values if present
                        fields[key].userValue = fields[key].userValue || fields[key].value;
                    }
                    fields.owner.userValue = fields.owner.userValue || Q.Users.Web3.getSelectedXid();
                    
                    fields.beneficiary.userValue = fields.beneficiary.userValue || Q.Users.Web3.getSelectedXid();

                    // validate (after user input and applied defaults value)
                    var validated = true;
                    for (let key in fields) {
                        for (let validateMethod in fields[key].validate) {
                            if (!Q.validate[validateMethod](fields[key].userValue)) {
                                validated = false;
                                Q.Notices.add({
                                    content: fields[key].validate[validateMethod].replace('%key%', key),
                                    timeout: 5
                                });
                                break;
                            }
                        }
                    }

                    // call produce
                    if (validated) {
                        // adjust values
                        
                        fields.price.userValue = ethers.utils.parseUnits(fields.price.userValue,18);
                        
                        tool.produce(
                            fields.NFTContract.userValue,
                            fields.seriesId.userValue,
                            fields.owner.userValue,
                            fields.currency.userValue,
                            fields.price.userValue,
                            fields.beneficiary.userValue,
                            fields.autoindex.userValue,
                            fields.duration.userValue,
                            fields.rateInterval.userValue,
                            fields.rateAmount.userValue,
                            function(err, obj, contract){
                                //console.log("tool.produce callback [arguments]= ",arguments)
                            }
                        );
                    }

                });
                
                $(".Assets_NFT_sales_factory_instancesList", tool.element).on(Q.Pointer.fastclick, function(){
                    tool._whitelistRefresh();
                });
                tool._whitelistRefresh();
                    
            }
        );
    }
    
});

Q.Template.set("Assets/NFT/sales/factory", 
    `<div>
        <div class="form">

            {{#unless fields.NFTContract.hide}}
            <!-- address NFTContract, -->
            <div class="form-group">
                <label>{{NFT.sales.factory.form.labels.NFTContract}}</label>
                <input name="NFTContract" type="text" class="form-control" value="{{fields.NFTContract.value}}" placeholder="{{NFT.sales.factory.placeholders.address}} {{NFT.sales.factory.placeholders.optional}}">
                <small class="form-text text-muted">{{NFT.sales.factory.form.small.NFTContract}}</small>
            </div>
            {{/unless}}
    
            {{#unless fields.owner.hide}}
            <div class="form-group">
                <label>{{NFT.sales.factory.form.labels.owner}}</label>
                <input name="owner" type="text" class="form-control" placeholder="{{NFT.sales.factory.placeholders.address}} {{NFT.sales.factory.placeholders.optional}}">
                <small class="form-text text-muted">{{NFT.sales.factory.form.small.owner}}</small>
            </div>
            {{/unless}}
            
            {{#unless fields.seriesId.hide}}
            <div class="form-group">
                <label>{{NFT.sales.factory.form.labels.seriesId}}</label>
                <input name="seriesId" type="text" class="form-control" placeholder="{{NFT.sales.factory.placeholders.number}}">
                <small class="form-text text-muted">{{NFT.sales.factory.form.small.seriesId}}</small>
            </div>
            {{/unless}}
    
            <!-- uint256 price, -->
            <div class="row">
                <div class="col-sm-6">
                    {{#unless fields.price.hide}}
                    <div class="form-group">
                        <label>{{NFT.sales.factory.form.labels.price}}</label>
                        <input name="price" type="text" class="form-control" placeholder="{{NFT.sales.factory.placeholders.fraction}}">
                        <small class="form-text text-muted">{{NFT.sales.factory.form.small.price}}</small>
                    </div>
                    {{/unless}}
                </div>
                <div class="col-sm-6">
                    {{#unless fields.currency.hide}}
                    <label>{{NFT.sales.factory.form.labels.currency}}</label>
                    <div class="form-group">
                    {{&tool "Assets/web3/currencies" chainId=chainId }}
                    </div>
                    {{/unless}}
                </div>
            </div>
            {{#unless fields.beneficiary.hide}}
            <!-- address beneficiary, -->
            <div class="form-group">
                <label>{{NFT.sales.factory.form.labels.beneficiary}}</label>
                <input name="beneficiary" type="text" class="form-control" placeholder="{{NFT.sales.factory.placeholders.address}} {{NFT.sales.factory.placeholders.optional}}">
                <small class="form-text text-muted">{{NFT.sales.factory.form.small.beneficiary}}</small>
            </div>
            {{/unless}}
            <div class="row">
                <div class="col-sm-6">
                    {{#unless fields.autoindex.hide}}
                    <!-- uint192 autoindex, -->
                    <div class="form-group">
                        <label>{{NFT.sales.factory.form.labels.autoindex}}</label>
                        <input name="autoindex" type="text" class="form-control" placeholder="{{NFT.sales.factory.placeholders.integer}}">
                        <small class="form-text text-muted">{{NFT.sales.factory.form.small.autoindex}}</small>
                    </div>
                    {{/unless}}
                    {{#unless fields.duration.hide}}
                    <!-- uint64 duration, -->
                    <div class="form-group">
                        <label>{{NFT.sales.factory.form.labels.duration}}</label>
                        <input name="duration" type="text" class="form-control" placeholder="{{NFT.sales.factory.placeholders.integer}}">
                        <small class="form-text text-muted">{{NFT.sales.factory.form.small.duration}}</small>
                    </div>
                    {{/unless}}
                </div>
                <div class="col-sm-6">
                    {{#unless fields.rateInterval.hide}}
                    <!-- uint32 rateInterval, -->
                    <div class="form-group">
                        <label>{{NFT.sales.factory.form.labels.rateInterval}}</label>
                        <input name="rateInterval" type="text" class="form-control" placeholder="{{NFT.sales.factory.placeholders.integer}}">
                        <small class="form-text text-muted">{{NFT.sales.factory.form.small.rateInterval}}</small>
                    </div>
                    {{/unless}}
                    {{#unless fields.rateAmount.hide}}
                    <!-- uint16 rateAmount -->
                    <div class="form-group">
                        <label>{{NFT.sales.factory.form.labels.rateAmount}}</label>
                        <input name="rateAmount" type="text" class="form-control" placeholder="{{NFT.sales.factory.placeholders.integer}}">
                        <small class="form-text text-muted">{{NFT.sales.factory.form.small.rateAmount}}</small>
                    </div>
                    {{/unless}}
                </div>
            </div>
    
            <button class="Assets_NFT_sales_factory_produce Q_button">{{NFT.sales.factory.produce}}</button>
            <button class="Assets_NFT_sales_factory_instancesList Q_button">{{NFT.sales.factory.viewInstancesList}}</button>
            <div>
                <h3>List by NFT</h3>
                <table class="Assets_NFT_sales_factory_instancesTableList">
                <tr class="Assets_NFT_sales_factory_loading" style="display:none"><td>Loading ...</td></tr>
                </table>
            </div>
        </div>
    
    </div>
    
    `,
{ text: ["Assets/content"] }
);

})(window, Q, jQuery);