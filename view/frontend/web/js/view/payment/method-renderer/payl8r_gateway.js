
/*browser:true*/
/*global define*/
define(
        [
          'mage/storage',
          'Magento_Checkout/js/view/payment/default',
          'Magento_Payl8rPaymentGateway/js/model/iframe',
          'Magento_Checkout/js/model/full-screen-loader',
          'Magento_Checkout/js/model/quote',
          'Magento_Checkout/js/model/url-builder',
        ],
        function (storage, Component, iframe, fullScreenLoader, quote, urlBuilder) {
          'use strict';

          return Component.extend({
            defaults: {
              template: 'Magento_Payl8rPaymentGateway/payment/form',
//        transactionResult: '',
              paymentReady: false
            },
            redirectAfterPlaceOrder: false,
            isInAction: iframe.isInAction,
            initObservable: function () {

              this._super()
                      .observe([
//            'transactionResult',
                        'paymentReady'
                      ]);
              return this;
            },
            isPaymentReady: function () {
              return this.paymentReady();
            },
            getActionUrl: function () {
              return this.isInAction() ? window.checkoutConfig.payment[this.getCode()].actionUrl : '';
            },
            placePendingPaymentOrder: function () {
              console.log('PLACE PENDING.....');
              console.log(this.getData());
              console.log(quote.billingAddress());
              this.getIframeData();
              return;
              if (this.placeOrder()) {
                fullScreenLoader.startLoader();
                this.isInAction(true);
                // capture all click events
                document.addEventListener('click', iframe.stopEventPropagation, true);
              }
            },
            getPlaceOrderDeferredObject: function () {
              console.log('PLACE ORDER DEFFERED.....');
              var self = this;
              return this._super().fail(function () {
                fullScreenLoader.stopLoader();
                self.isInAction(false);
                document.removeEventListener('click', iframe.stopEventPropagation, true);
              });
            },
            afterPlaceOrder: function () {
              console.log('AFTER PLACING.....');
              if (this.iframeIsLoaded) {
                document.getElementById(this.getCode() + '-iframe')
                        .contentWindow.location.reload();
              }

              this.paymentReady(true);
              this.iframeIsLoaded = true;
              this.isPlaceOrderActionAllowed(true);
              fullScreenLoader.stopLoader();
            },
            beforePlaceOrder: function (data) {
              console.log('BEFORE PLACING');
              console.log(data);
            },
            iframeLoaded: function () {
              console.log('IFRAME LOADED!');
              fullScreenLoader.stopLoader();
            },

            getIframeData: function () {
//            fullScreenLoader.startLoader();

              var payload = {
                cartId: quote.getQuoteId(),
                billingAddress: quote.billingAddress(),
                paymentMethod: this.getData(),
                email: quote.guestEmail
              };
              var serviceUrl = urlBuilder.createUrl('/payl8r/guest-carts/:quoteId/payment-information', {
                quoteId: quote.getQuoteId()
              });
              return storage.post(
                      serviceUrl, JSON.stringify(payload)
                      ).fail(
                      function (response) {
//                    errorProcessor.process(response, messageContainer);
                      }
              ).always(
                      function () {
                        fullScreenLoader.stopLoader();
                      }
              );

            },
            getCode: function () {
              return 'payl8r_gateway';
            },
            getLogo: function () {
              return require.toUrl('Magento_Payl8rPaymentGateway') + '/images/payl8rlogo.png';
            },
            getData: function () {
              return {
                'method': this.item.method,
                'additional_data': null
              };
            },
//      getTransactionResults: function () {
//        return _.map(window.checkoutConfig.payment.payl8r_gateway.transactionResults, function (value, key) {
//          return {
//            'value': key,
//            'transaction_result': value
//          }
//        });
//      }
          });
        }
);