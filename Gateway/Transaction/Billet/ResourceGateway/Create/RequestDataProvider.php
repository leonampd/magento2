<?php
/**
 * Class RequestDataProvider
 */

namespace PagarMe\Magento2\Gateway\Transaction\Billet\ResourceGateway\Create;


use Magento\Checkout\Model\Session;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Model\InfoInterface;
use PagarMe\Magento2\Api\BilletRequestDataProviderInterface;
use PagarMe\Magento2\Gateway\Transaction\Base\ResourceGateway\AbstractRequestDataProvider;
use PagarMe\Magento2\Gateway\Transaction\Billet\Config\ConfigInterface;
use PagarMe\Magento2\Helper\CustomerAddressInterface;

class RequestDataProvider
    extends AbstractRequestDataProvider
    implements BilletRequestDataProviderInterface
{
    protected $config;

    public function __construct (
        OrderAdapterInterface $orderAdapter,
        InfoInterface $payment,
        Session $session,
        CustomerAddressInterface $customerAddressHelper,
        ConfigInterface $config
    )
    {
        parent::__construct($orderAdapter, $payment, $session, $customerAddressHelper);
        $this->setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getBankType()
    {
        return $this->getConfig()->getTypeBank();
    }

    /**
     * {@inheritdoc}
     */
    public function getInstructions()
    {
        return $this->getConfig()->getInstructions();
    }

    /**
     * {@inheritdoc}
     */
    public function getDaysToAddInBoletoExpirationDate()
    {
        return $this->getConfig()->getExpirationDays();
    }

    /**
     * @return ConfigInterface
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @param ConfigInterface $config
     * @return $this
     */
    protected function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerAddressStreet($shipping)
    {
        if ($shipping) {
            return $this->getShippingAddressAttribute($this->getConfig()->getCustomerStreetAttribute());
        }

        return $this->getBillingAddressAttribute($this->getConfig()->getCustomerStreetAttribute());
    }

    /**
     * @return string
     */
    public function getCustomerAddressNumber($shipping)
    {
        if ($shipping) {
            return $this->getShippingAddressAttribute($this->getConfig()->getCustomerAddressNumber());
        }
        
        return $this->getBillingAddressAttribute($this->getConfig()->getCustomerAddressNumber());
    }

    /**
     * @return string
     */
    public function getCustomerAddressComplement($shipping)
    {
        if ($shipping) {
            return $this->getShippingAddressAttribute($this->getConfig()->getCustomerAddressComplement());
        }
        
        return $this->getBillingAddressAttribute($this->getConfig()->getCustomerAddressComplement());
    }

    /**
     * @return string
     */
    public function getCustomerAddressDistrict($shipping)
    {
        if ($shipping) {
            return $this->getShippingAddressAttribute($this->getConfig()->getCustomerAddressDistrict());
        }
        
        return $this->getBillingAddressAttribute($this->getConfig()->getCustomerAddressDistrict());
    }
}
