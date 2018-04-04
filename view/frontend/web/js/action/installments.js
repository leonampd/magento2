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

        return function () {
            var serviceUrl;
            serviceUrl = urlBuilder.createUrl('/pagarme/creditcard/installments/', {});

            return storage.post(
                serviceUrl, false
            )
        };
    }
);
