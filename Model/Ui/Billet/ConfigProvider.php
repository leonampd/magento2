<?php
/**
 * Class ConfigProvider
 */

namespace PagarMe\Magento2\Model\Ui\Billet;


use Magento\Checkout\Model\ConfigProviderInterface;
use PagarMe\Magento2\Gateway\Transaction\Billet\Config\ConfigInterface;

final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'pagarme_billet';

    protected $billetConfig;

    /**
     * ConfigProvider constructor.
     * @param ConfigInterface $billetConfig
     */
    public function __construct(
        ConfigInterface $billetConfig
    )
    {
        $this->setBilletConfig($billetConfig);
    }

    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE =>[
                    'text' => $this->getBilletConfig()->getText()
                ]
            ]
        ];
    }

    /**
     * @return ConfigInterface
     */
    protected function getBilletConfig()
    {
        return $this->billetConfig;
    }

    /**
     * @param ConfigInterface $billetConfig
     * @return $this
     */
    protected function setBilletConfig(ConfigInterface $billetConfig)
    {
        $this->billetConfig = $billetConfig;
        return $this;
    }
}
