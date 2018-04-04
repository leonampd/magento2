<?php
/**
 * Class CreateCreditCardDataProviderInterface
 */

namespace PagarMe\Magento2\Api;


interface BilletRequestDataProviderInterface extends BaseRequestDataProviderInterface
{
    /**
     * @return string
     */
    public function getBankType();

    /**
     * @return string
     */
    public function getInstructions();

    /**
     * @return string
     */
    public function getDaysToAddInBoletoExpirationDate();

    /**
     * @return string
     */
    public function getCustomerAddressStreet($shipping);

    /**
     * @return string
     */
    public function getCustomerAddressNumber($shipping);

    /**
     * @return string
     */
    public function getCustomerAddressComplement($shipping);

    /**
     * @return string
     */
    public function getCustomerAddressDistrict($shipping);
}
