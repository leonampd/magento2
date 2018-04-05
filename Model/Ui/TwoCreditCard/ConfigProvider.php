<?php
/**
 * Class ConfigProvider
 */

namespace PagarMe\Magento2\Model\Ui\TwoCreditCard;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session;
use PagarMe\Magento2\Model\CardsFactory;
use PagarMe\Magento2\Gateway\Transaction\TwoCreditCard\Config\ConfigInterface;

final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'pagarme_two_creditcard';

    protected $creditCardConfig;

    protected $customerSession;

    protected $cardsFactory;

    /**
     * ConfigProvider constructor.
     * @param ConfigInterface $creditCardConfig
     */
    public function __construct(
        Session $customerSession,
        ConfigInterface $creditCardConfig,
        CardsFactory $cardsFactory
    )
    {
        $this->setCreditCardConfig($creditCardConfig);
        $this->setCustomerSession($customerSession);
        $this->setCardsFactory($cardsFactory);
    }

    public function getConfig()
    {
        $selectedCard = '';
        $cards = [];

        if ($this->getCustomerSession()->isLoggedIn()) {
            $is_saved_card = 0;
            
            $cards = [];
            $idCustomer = $this->getCustomerSession()->getCustomer()->getId();

            $model = $this->getCardsFactory();
            $cardsCollection = $model->getCollection()->addFieldToFilter('customer_id',array('eq' => $idCustomer));

            foreach ($cardsCollection as $card) {
                $is_saved_card = 1;
                $cards[] = [
                    'id' => $card->getId(),
                    'last_four_numbers' => $card->getLastFourNumbers(),
                    'brand' => $card->getBrand(),
                ];
                $selectedCard = $card->getId();
            }

        }else{
            $is_saved_card = 0;
        }
        
        return [
            'payment' => [
                self::CODE =>[
                    'active' => $this->getCreditCardConfig()->getActive(),
                    'is_saved_card' => $is_saved_card,
                    'cards' => $cards,
                    'selected_card' => $selectedCard
                ]
            ]
        ];
    }

    /**
     * @return ConfigInterface
     */
    protected function getCreditCardConfig()
    {
        return $this->creditCardConfig;
    }

    /**
     * @param ConfigInterface $creditCardConfig
     * @return $this
     */
    protected function setCreditCardConfig(ConfigInterface $creditCardConfig)
    {
        $this->creditCardConfig = $creditCardConfig;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomerSession()
    {
        return $this->customerSession;
    }

    /**
     * @param mixed $customerSession
     *
     * @return self
     */
    public function setCustomerSession($customerSession)
    {
        $this->customerSession = $customerSession;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCardsFactory()
    {
        return $this->cardsFactory->create();
    }

    /**
     * @param mixed $cardsFactory
     *
     * @return self
     */
    public function setCardsFactory($cardsFactory)
    {
        $this->cardsFactory = $cardsFactory;

        return $this;
    }
}
