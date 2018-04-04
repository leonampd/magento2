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

        return function (brand) {
            var serviceUrl;
            serviceUrl = urlBuilder.createUrl('/pagarme/installments/brand/' + brand, {});

            return storage.get(
                serviceUrl, false
            )
        };
    }
);
