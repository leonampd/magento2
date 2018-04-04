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
                type: 'pagarme_creditcard',
                component: 'PagarMe_Magento2/js/view/payment/method-renderer/creditcard'
            },
            {
                type: 'pagarme_billet_creditcard',
                component: 'PagarMe_Magento2/js/view/payment/method-renderer/billet_creditcard'
            },
            {
                type: 'pagarme_billet',
                component: 'PagarMe_Magento2/js/view/payment/method-renderer/billet'
            },
            {
                type: 'pagarme_two_creditcard',
                component: 'PagarMe_Magento2/js/view/payment/method-renderer/two_creditcard'
            }
        );
        return Component.extend({});
    }
);
