<?php
/**
 * Class ConfigInterface
 */

namespace PagarMe\Magento2\Gateway\Transaction\Billet\Config;


interface ConfigInterface
{
    const PATH_INSTRUCTIONS     = 'payment/pagarme_billet/instructions';
    const PATH_TEXT             = 'payment/pagarme_billet/text';
    const PATH_TYPE_BANK        = 'payment/pagarme_billet/types';
    const PATH_EXPIRATION_DAYS  = 'payment/pagarme_billet/expiration_days';
    const PATH_CUSTOMER_STREET              = 'payment/pagarme_customer_address/street_attribute';
    const PATH_CUSTOMER_NUMBER              = 'payment/pagarme_customer_address/number_attribute';
    const PATH_CUSTOMER_COMPLEMENT          = 'payment/pagarme_customer_address/complement_attribute';
    const PATH_CUSTOMER_DISTRICT            = 'payment/pagarme_customer_address/district_attribute';

    /**
     * @return string
     */
    public function getInstructions();

    /**
     * @return string
     */
    public function getText();

    /**
     * @return string
     */
    public function getTypeBank();

    /**
     * @return string
     */
    public function getExpirationDays();
    
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
