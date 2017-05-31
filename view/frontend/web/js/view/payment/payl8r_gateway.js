
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'payl8r_gateway',
                component: 'Magento_Payl8rPaymentGateway/js/view/payment/method-renderer/payl8r_gateway'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({
            redirectAfterPlaceOrder: function() {
              console.log('DUPA');
            },

          
        });
    }
);
