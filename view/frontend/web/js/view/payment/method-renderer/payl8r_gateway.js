
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magento_Payl8rPaymentGateway/payment/form',
                transactionResult: ''
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult'
                    ]);
                return this;
            },
            afterPlaceOrder:function() {
              console.log('DUPA 3');
            },
            

            redirectAfterPlaceOrder: function() {
              console.log('DUPA 2');
            },
            
            getCode: function() {
                return 'payl8r_gateway';
            },

            getLogo: function() {
                return require.toUrl('Magento_Payl8rPaymentGateway')+'/images/payl8rlogo.png';
            },
            
            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_result': this.transactionResult()
                    }
                };
            },

            getTransactionResults: function() {
                return _.map(window.checkoutConfig.payment.payl8r_gateway.transactionResults, function(value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            }
        });
    }
);