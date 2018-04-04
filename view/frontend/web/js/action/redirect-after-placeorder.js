/*browser:true*/
/*global define*/
define(
    [
        'mage/storage',
        'Magento_Checkout/js/model/url-builder'
    ],
    function (
        storage,
        urlBuilder
    ) {
        'use strict';

        return function (orderId) {
            var serviceUrl;
            serviceUrl = urlBuilder.createUrl('/pagarme/redirect-after-placeorder/:orderId/link', {
                orderId: orderId
            });

            return storage.post(
                serviceUrl, false
            );
        };
    }
);
