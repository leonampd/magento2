<?php
/**
 * Class ConfigInterface
 */

namespace PagarMe\Magento2\Gateway\Transaction\CreditCard\Config;


interface ConfigInterface
{
    const PATH_ACTIVE                       = 'payment/pagarme_creditcard/active';
    const PATH_PAYMENT_ACTION               = 'payment/pagarme_creditcard/payment_action';
    const PATH_ANTIFRAUD_ACTIVE             = 'payment/pagarme_creditcard/antifraud_active';
    const PATH_ANTIFRAUD_MIN_AMOUNT         = 'payment/pagarme_creditcard/antifraud_min_amount';
    const PATH_SOFT_DESCRIPTION             = 'payment/pagarme_creditcard/soft_description';
    const PATH_CUSTOMER_STREET              = 'payment/pagarme_customer_address/street_attribute';
    const PATH_CUSTOMER_NUMBER              = 'payment/pagarme_customer_address/number_attribute';
    const PATH_CUSTOMER_COMPLEMENT          = 'payment/pagarme_customer_address/complement_attribute';
    const PATH_CUSTOMER_DISTRICT            = 'payment/pagarme_customer_address/district_attribute';

    /**
     * @return bool
     */
    public function getActive();

    /**
     * @return string
     */
    public function getPaymentAction();

    /**
     * @return bool
     */
    public function getAntifraudActive();

    /**
     * @return string
     */
    public function getAntifraudMinAmount();

    /**
     * @return string
     */
    public function getSoftDescription();

    /**
     * @return string
     */
    public function getCustomerStreetAttribute();

    /**
     * @return string
     */
    public function getCustomerAddressNumber();

    /**
     * @return string
     */
    public function getCustomerAddressComplement();

    /**
     * @return string
     */
    public function getCustomerAddressDistrict();
}
