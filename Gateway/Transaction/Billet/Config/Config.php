<?php
/**
 * Class Config
 */

namespace PagarMe\Magento2\Gateway\Transaction\Billet\Config;


use PagarMe\Magento2\Gateway\Transaction\Base\Config\AbstractConfig;

class Config extends AbstractConfig implements ConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getInstructions()
    {
        return $this->getConfig(static::PATH_INSTRUCTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function getText()
    {
        return $this->getConfig(static::PATH_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeBank()
    {
        return $this->getConfig(static::PATH_TYPE_BANK);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationDays()
    {
        return $this->getConfig(static::PATH_EXPIRATION_DAYS);
    }

    /**
     * @return string
     */
    public function getCustomerStreetAttribute()
    {
        return $this->getConfig(static::PATH_CUSTOMER_STREET);
    }

    /**
     * @return string
     */
    public function getCustomerAddressNumber()
    {
        return $this->getConfig(static::PATH_CUSTOMER_NUMBER);
    }
    
    /**
     * @return string
     */
    public function getCustomerAddressComplement()
    {
        return $this->getConfig(static::PATH_CUSTOMER_COMPLEMENT);
    }

    /**
     * @return string
     */
    public function getCustomerAddressDistrict()
    {
        return $this->getConfig(static::PATH_CUSTOMER_DISTRICT);
    }
}
