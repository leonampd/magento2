<?php
/**
 * Class Config
 */

namespace PagarMe\Magento2\Gateway\Transaction\Base\Config;


class Config extends AbstractConfig implements ConfigInterface
{
    /**
     * @return string
     */
    public function getSecretKey()
    {
        if ($this->getTestMode()) {
            return $this->getConfig(static::PATH_SECRET_KEY_TEST);
        }
        
        return $this->getConfig(static::PATH_SECRET_KEY);
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        if ($this->getTestMode()) {
            return $this->getConfig(static::PATH_PUBLIC_KEY_TEST);
        }
        
        return $this->getConfig(static::PATH_PUBLIC_KEY);
    }

    /**
     * @return string
     */
    public function getTestMode()
    {
        return $this->getConfig(static::PATH_TEST_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        if ($this->getConfig(static::PATH_TEST_MODE)) {
            return $this->getConfig(static::PATH_SAND_BOX_URL);
        }

        return $this->getConfig(static::PATH_PRODUCTION_URL);
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
