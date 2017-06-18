
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
          'Magento_Customer/js/model/customer',
          'Magento_Customer/js/customer-data'
        ],
        function (storage, Component, iframe, fullScreenLoader, quote, urlBuilder, customer, customerData) {
          'use strict';

          return Component.extend({
            defaults: {
              template: 'Magento_Payl8rPaymentGateway/payment/form',
//        transactionResult: '',
              paymentReady: false,
              iframeAction: '',
              iframeUsername: '',
              iframePayload: '',
            },
            redirectAfterPlaceOrder: false,
            isInAction: iframe.isInAction,
            initObservable: function () {

              this._super().observe([
                'paymentReady', 'iframeAction', 'iframeUsername', 'iframePayload'
              ]);
              return this;
            },
            isPaymentReady: function () {
              return this.paymentReady();
            },
            getIframeAction: function () {
              return this.iframeAction();
            },
            getIframeUsername: function () {
              return this.iframeUsername();
            },
            getIframePayload: function () {
              return this.iframePayload();
            },
            getActionUrl: function () {
              return this.isInAction() ? window.checkoutConfig.payment[this.getCode()].actionUrl : '';
            },
            placePendingPaymentOrder: function () {
              console.log('PLACE PENDING.....');
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

//              return this._super().fail(function () {
              return this.placeOrderPayl8r().fail(function () {
                fullScreenLoader.stopLoader();
                self.isInAction(false);
                document.removeEventListener('click', iframe.stopEventPropagation, true);
              });
            },
            afterPlaceOrder: function () {
              console.log('AFTER PLACING.....');
              // invalidating session to clear cart...
              var sections = ['cart'];
              customerData.invalidate(sections);
              customerData.reload(sections, true);
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

            placeOrderPayl8r: function () {
//            fullScreenLoader.startLoader();
              var self = this, serviceUrl;
              var payload = {
                cartId: quote.getQuoteId(),
                billingAddress: quote.billingAddress(),
                paymentMethod: this.getData(),
              };
//              var serviceUrl = urlBuilder.createUrl('/payl8r/guest-carts/:quoteId/payment-information', {
//                quoteId: quote.getQuoteId()
//              });

              if (customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/payl8r/carts/mine/payment-information', {});
              } else {
                serviceUrl = urlBuilder.createUrl('/payl8r/guest-carts/:quoteId/payment-information', {
                  quoteId: quote.getQuoteId()
                });
                payload.email = quote.guestEmail;
              }

              return storage.post(serviceUrl, JSON.stringify(payload))
                      .done(
                              function (response) {
                                if ('undefined' !== response[1] && response[1]) {
                                  self.paymentReady(true);
                                  self.iframeAction(response[1].action);
                                  self.iframeUsername(response[1].rid);
                                  self.iframePayload(response[1].data);
                                  window.addEventListener("message", pl_iframe_heightUpdate, false);
                                  var prevHeight = jQuery('[name="payl8rFrame"]').height();
                                  function pl_iframe_heightUpdate(event) {
                                    var origin = event.origin || event.originalEvent.origin;
                                    if (origin !== "https://payl8r.com")
                                      return;
                                    if (prevHeight !== jQuery('[name="payl8rFrame"]').height())
                                      prevHeight = event.data;
                                    jQuery('[name="payl8rFrame"]').height(event.data);
                                  }
                                  document.getElementById("payl8rForm").submit();
                                }
                              }
                      )
                      .fail(
                              function (response) {
//                    errorProcessor.process(response, messageContainer);
                              }
                      )
                      .always(
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