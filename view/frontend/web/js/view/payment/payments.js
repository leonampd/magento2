/**
 * @author      MundiPagg Embeddables Team <embeddables@magento2.com>
 * @copyright   2017 MundiPagg (http://www.magento2.com)
 * @license     http://www.magento2.com  Copyright
 *
 * @link        http://www.magento2.com
 */
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
