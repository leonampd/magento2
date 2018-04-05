<?php
/**
 * Class RequestBuilder
 */

namespace PagarMe\Magento2\Gateway\Transaction\TwoCreditCard\ResourceGateway\Create;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Item;
use PagarMe\Magento2\Api\CartItemRequestDataProviderInterface;
use PagarMe\Magento2\Api\CreditCardRequestDataProviderInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use MundiAPILib\Models\CreateOrderRequest as Request;
use PagarMe\Magento2\Api\CreditCardRequestDataProviderInterfaceFactory;
use PagarMe\Magento2\Api\CartItemRequestDataProviderInterfaceFactory;
use Magento\Checkout\Model\Cart;
use PagarMe\Magento2\Gateway\Transaction\Base\Config\Config;
use PagarMe\Magento2\Gateway\Transaction\TwoCreditCard\Config\Config as ConfigCreditCard;
use PagarMe\Magento2\Helper\ModuleHelper;
use PagarMe\Magento2\Model\CardsFactory;
use PagarMe\Magento2\Helper\Logger;

class RequestBuilder implements BuilderInterface
{

    const MODULE_NAME = 'PagarMe_Magento2';
    const NAME_METADATA = 'Magento 2';
    const SHIPPING = 1;
    const BILLING = 0;
    
    protected $request;
    protected $requestDataProviderFactory;
    protected $cartItemRequestDataProviderFactory;
    protected $orderAdapter;
    protected $paymentData;
    protected $cart;
    protected $config;
    protected $configCreditCard;
    protected $moduleHelper;
    protected $cardsFactory;

    /**
     * @var \PagarMe\Magento2\Helper\Logger
     */
    protected $logger;

    /**
     * RequestBuilder constructor.
     * @param Request $request
     * @param CreditCardRequestDataProviderInterfaceFactory $requestDataProviderFactory
     * @param CartItemRequestDataProviderInterfaceFactory $cartItemRequestDataProviderFactory
     * @param Cart $cart
     * @param Config $config
     * @param ConfigCreditCard $configCreditCard
     * @param ModuleHelper $moduleHelper
     */
    public function __construct(
        Request $request,
        CreditCardRequestDataProviderInterfaceFactory $requestDataProviderFactory,
        CartItemRequestDataProviderInterfaceFactory $cartItemRequestDataProviderFactory,
        Cart $cart,
        Config $config,
        ConfigCreditCard $configCreditCard,
        ModuleHelper $moduleHelper,
        CardsFactory $cardsFactory,
        Logger $logger
    )
    {
        $this->setRequest($request);
        $this->setRequestDataProviderFactory($requestDataProviderFactory);
        $this->setCartItemRequestProviderFactory($cartItemRequestDataProviderFactory);
        $this->setCart($cart);
        $this->setConfig($config);
        $this->setConfigCreditCard($configCreditCard);
        $this->setModuleHelper($moduleHelper);
        $this->setCardsFactory($cardsFactory);
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDataObject */
        $paymentDataObject = $buildSubject['payment'];

        $this->setOrderAdapter($paymentDataObject->getOrder());
        $this->setPaymentData($paymentDataObject->getPayment());

        $requestDataProvider = $this->createRequestDataProvider();

        return $this->createNewRequest($requestDataProvider);

    }

    /**
     * @param Request $request
     * @return $this
     */
    protected function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @return CreditCardRequestDataProviderInterface
     */
    protected function createRequestDataProvider()
    {
        return $this->getRequestDataProviderFactory()->create([
            'orderAdapter' => $this->getOrderAdapter(),
            'payment' => $this->getPaymentData()
        ]);
    }

    /**
     * @param Item $item
     * @return CartItemRequestDataProviderInterface
     */
    protected function createCartItemRequestDataProvider(Item $item)
    {
        return $this->getCartItemRequestProviderFactory()->create([
            'item' => $item
        ]);
    }

    /**
     * @return RequestDataProviderFactory
     */
    protected function getRequestDataProviderFactory()
    {
        return $this->requestDataProviderFactory;
    }

    /**
     * @param CreditCardRequestDataProviderInterfaceFactory $requestDataProviderFactory
     * @return RequestBuilder
     */
    protected function setRequestDataProviderFactory(CreditCardRequestDataProviderInterfaceFactory $requestDataProviderFactory)
    {
        $this->requestDataProviderFactory = $requestDataProviderFactory;
        return $this;
    }

    /**
     * @return CartItemRequestDataProviderInterfaceFactory
     */
    protected function getCartItemRequestProviderFactory()
    {
        return $this->cartItemRequestDataProviderFactory;
    }

    /**
     * @param CartItemRequestDataProviderInterfaceFactory $cartItemRequestDataProviderFactory
     * @return self
     */
    protected function setCartItemRequestProviderFactory(CartItemRequestDataProviderInterfaceFactory $cartItemRequestDataProviderFactory)
    {
        $this->cartItemRequestDataProviderFactory = $cartItemRequestDataProviderFactory;
        return $this;
    }


    /**
     * @return OrderAdapterInterface
     */
    protected function getOrderAdapter()
    {
        return $this->orderAdapter;
    }

    /**
     * @param OrderAdapterInterface $orderAdapter
     * @return $this
     */
    protected function setOrderAdapter(OrderAdapterInterface $orderAdapter)
    {
        $this->orderAdapter = $orderAdapter;
        return $this;
    }

    /**
     * @return InfoInterface
     */
    public function getPaymentData()
    {
        return $this->paymentData;
    }

    /**
     * @param InfoInterface $paymentData
     * @return $this
     */
    protected function setPaymentData(InfoInterface $paymentData)
    {
        $this->paymentData = $paymentData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return mixed
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfigCreditCard()
    {
        return $this->configCreditCard;
    }

    /**
     * @return mixed
     */
    public function setConfigCreditCard($configCreditCard)
    {
        $this->configCreditCard = $configCreditCard;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModuleHelper()
    {
        return $this->moduleHelper;
    }

    /**
     * @return mixed
     */
    public function setModuleHelper($moduleHelper)
    {
        $this->moduleHelper = $moduleHelper;

        return $this;
    }

    /**
     * @return \MundiAPILib\MundiAPIClient
     */
    public function getApi()
    {
        return new \MundiAPILib\MundiAPIClient($this->getConfig()->getSecretKey(), '');
    }

    /**
     * @return \MundiAPILib\Models\CreateOrderRequest
     */
    public function getOrderRequest()
    {
        return new \MundiAPILib\Models\CreateOrderRequest();
    }

    /**
     * @return \MundiAPILib\Models\CreateTokenRequest
     */
    public function getTokenRequest()
    {
        return new \MundiAPILib\Models\CreateTokenRequest();
    }

    protected function createTokenCard($requestDataProvider)
    {
        $request = $this->getTokenRequest();

        $request->card = [
            "type" => "credit",
            "number" => $requestDataProvider->getCreditCardNumber(),
            "holder_name" => $requestDataProvider->getHolderName(),
            "exp_month" => $requestDataProvider->getExpMonth(),
            "exp_year" => $requestDataProvider->getExpYear(),
            "cvv" => $requestDataProvider->getSecurityCode()
        ];

        $request->metadata = [
            'module_name' => self::NAME_METADATA,
            'module_version' => $this->getModuleHelper()->getVersion(self::MODULE_NAME),
        ];

        try {

            $token = $this->getApi()->getTokens()->createToken($this->getConfig()->getPublicKey(), $request);

        } catch (\MundiAPILib\Exceptions\ErrorException $error) {
            throw new \InvalidArgumentException($error);
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException($ex->getMessage());
        }


        return $token->id;
    }

    protected function createTokenMultipleCards($payment, $type)
    {
        $request = $this->getTokenRequest();

        if ($type == 'First') {
            $request->card = [
                'type' => 'credit',
                'number' => $payment->getCcNumberFirst(),
                'holder_name' => $payment->getCcOwnerFirst(),
                'exp_month' => $payment->getCcExpMonthFirst(),
                'exp_year' => $payment->getCcExpYearFirst(),
                'cvv' => $payment->getCcCidFirst()
            ];
        }

        if ($type == 'Second') {
            $request->card = [
                'type' => 'credit',
                'number' => $payment->getCcNumberSecond(),
                'holder_name' => $payment->getCcOwnerSecond(),
                'exp_month' => $payment->getCcExpMonthSecond(),
                'exp_year' => $payment->getCcExpYearSecond(),
                'cvv' => $payment->getCcCidSecond()
            ];
        }

        $request->metadata = [
            'module_name' => self::NAME_METADATA,
            'module_version' => $this->getModuleHelper()->getVersion(self::MODULE_NAME),
        ];

        try {

            $token = $this->getApi()->getTokens()->createToken($this->getConfig()->getPublicKey(), $request);

        } catch (\MundiAPILib\Exceptions\ErrorException $error) {
            throw new \InvalidArgumentException($error);
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException($ex->getMessage());
        }


        return $token->id;
    }

    /**
     * @param $requestDataProvider
     * @return mixed
     */
    protected function createNewRequest($requestDataProvider)
    {

        $quote = $this->getCart()->getQuote();

        $payment = $quote->getPayment();

        $order = $this->getOrderRequest();

        $statement = $this->getConfigCreditCard()->getSoftDescription();

        if($this->getConfigCreditCard()->getPaymentAction() == 'authorize_capture'){
            $capture = true;
        }else{
            $capture = false;
        }
        
        $model = $this->getCardsFactory();

        $order->payments = [
            [
                'payment_method' => 'credit_card',
                'amount' => str_replace('.', '', ($payment->getCcFirstCardAmount() + $payment->getAdditionalInformation('cc_first_card_tax_amount')) * 100),
                'credit_card' => [
                    'recurrence' => false,
                    'capture' => $capture,
                    'statement_descriptor' => $statement,
                    'installments' => $payment->getAdditionalInformation('cc_installments_first'),
                    'card' => [
                        'billing_address' => [
                            'street' => $requestDataProvider->getCustomerAddressStreet(self::BILLING),
                                'number' => $requestDataProvider->getCustomerAddressNumber(self::BILLING),
                                'complement' => $requestDataProvider->getCustomerAddressComplement(self::BILLING),
                                'zip_code' => trim(str_replace('-','',$quote->getBillingAddress()->getPostCode())),
                                'neighborhood' => $requestDataProvider->getCustomerAddressDistrict(self::BILLING),
                                'city' => $quote->getBillingAddress()->getCity(),
                                'state' => $quote->getBillingAddress()->getRegionCode(),
                                'country' => $quote->getBillingAddress()->getCountryId()
                        ]
                    ]
                ]
            ],
            [
                'amount' => str_replace('.', '', ($payment->getCcSecondCardAmount() + $payment->getAdditionalInformation('cc_second_card_tax_amount')) * 100),
                'payment_method' => 'credit_card',
                'credit_card' => [
                    'recurrence' => false,
                    'capture' => $capture,
                    'statement_descriptor' => $statement,
                    'installments' => $payment->getAdditionalInformation('cc_installments_second'),
                    'card' => [
                        'billing_address' => [
                            'street' => $requestDataProvider->getCustomerAddressStreet(self::BILLING),
                                'number' => $requestDataProvider->getCustomerAddressNumber(self::BILLING),
                                'complement' => $requestDataProvider->getCustomerAddressComplement(self::BILLING),
                                'zip_code' => trim(str_replace('-','',$quote->getBillingAddress()->getPostCode())),
                                'neighborhood' => $requestDataProvider->getCustomerAddressDistrict(self::BILLING),
                                'city' => $quote->getBillingAddress()->getCity(),
                                'state' => $quote->getBillingAddress()->getRegionCode(),
                                'country' => $quote->getBillingAddress()->getCountryId()
                        ]
                    ]

                ]
            ]
        ];

        if (!empty($payment->getAdditionalInformation('cc_saved_card_first'))) {
            $cardCollection = $model->getCollection()->addFieldToFilter('id', array('eq' => $payment->getAdditionalInformation('cc_saved_card_first')))->getFirstItem();
            $order->payments[0]['credit_card']['card_id'] = $cardCollection->getCardToken();
        } else {
            $tokenFirstCard = $this->createTokenMultipleCards($payment, 'First');
            $order->payments[0]['credit_card']['card_token'] = $tokenFirstCard;
        }

        if (!empty($payment->getAdditionalInformation('cc_saved_card_second'))) {
            $cardCollection = $model->getCollection()->addFieldToFilter('id', array('eq' => $payment->getAdditionalInformation('cc_saved_card_second')))->getFirstItem();
            $order->payments[1]['credit_card']['card_id'] = $cardCollection->getCardToken();
        } else {
            $tokenSecondCard = $this->createTokenMultipleCards($payment, 'Second');
            $order->payments[1]['credit_card']['card_token'] = $tokenSecondCard;
        }

        $order->items = [];

        foreach ($requestDataProvider->getCartItems() as $key => $item) {

            $cartItemDataProvider = $this->createCartItemRequestDataProvider($item);

            $itemValues = [
                'amount' => $cartItemDataProvider->getUnitCostInCents(),
                'description' => $cartItemDataProvider->getName(),
                'quantity' => $cartItemDataProvider->getQuantity()
            ];

            array_push($order->items, $itemValues);

        }

        $document = $quote->getCustomerTaxvat() ? $quote->getCustomerTaxvat() : $quote->getShippingAddress()->getVatId() ;

        $order->customer = [
            'name' => !empty($requestDataProvider->getName()) ? $requestDataProvider->getName() : $quote->getBillingAddress()->getFirstName() . ' ' . $quote->getBillingAddress()->getLastName(),
            'email' => !empty($requestDataProvider->getEmail()) ? $requestDataProvider->getEmail() : $quote->getBillingAddress()->getEmail(),
            'document' => $document,
            'type' => 'individual',
            'address' => [
                'street' => $requestDataProvider->getCustomerAddressStreet(self::SHIPPING),
                'number' => $requestDataProvider->getCustomerAddressNumber(self::SHIPPING),
                'complement' => $requestDataProvider->getCustomerAddressComplement(self::SHIPPING),
                'zip_code' => trim(str_replace('-','',$quote->getShippingAddress()->getPostCode())),
                'neighborhood' => $requestDataProvider->getCustomerAddressDistrict(self::SHIPPING),
                'city' => $quote->getShippingAddress()->getCity(),
                'state' => $quote->getShippingAddress()->getRegionCode(),
                'country' => $quote->getShippingAddress()->getCountryId()
            ]
        ];

        $order->ip = $requestDataProvider->getIpAddress();

        $order->shipping = [
            'amount' => $quote->getShippingAddress()->getShippingAmount() * 100,
            'description' => $cartItemDataProvider->getName(),
            'address' => [
                'street' => $requestDataProvider->getCustomerAddressStreet(self::SHIPPING),
                'number' => $requestDataProvider->getCustomerAddressNumber(self::SHIPPING),
                'complement' => $requestDataProvider->getCustomerAddressComplement(self::SHIPPING),
                'zip_code' => trim(str_replace('-','',$quote->getShippingAddress()->getPostCode())),
                'neighborhood' => $requestDataProvider->getCustomerAddressDistrict(self::SHIPPING),
                'city' => $quote->getShippingAddress()->getCity(),
                'state' => $quote->getShippingAddress()->getRegionCode(),
                'country' => $quote->getShippingAddress()->getCountryId()
            ]
        ];

        $order->session_id = $requestDataProvider->getSessionId();

        $order->metadata = [
            'module_name' => self::NAME_METADATA,
            'module_version' => $this->getModuleHelper()->getVersion(self::MODULE_NAME),
        ];
        $quote->reserveOrderId()->save();
        $order->code = $quote->getReservedOrderId();

        if ($this->getConfigCreditCard()->getAntifraudActive() && $quote->getGrandTotal() > $this->getConfigCreditCard()->getAntifraudMinAmount()) {
            $order->antifraudEnabled = true;
        }

        try {
            $this->logger->logger($order->jsonSerialize());
            $response = $this->getApi()->getOrders()->createOrder($order);

            if ($payment->getAdditionalInformation('cc_savecard_first') == '1' && empty($payment->getAdditionalInformation('cc_saved_card_first'))) {
                $customer = $response->customer;
                $this->setCardToken($payment, $customer, $quote, 'FirstCard', $requestDataProvider);
            }

            if ($payment->getAdditionalInformation('cc_savecard_second') == '1' && empty($payment->getAdditionalInformation('cc_saved_card_second'))) {
                $customer = $response->customer;
                $this->setCardToken($payment, $customer, $quote, 'SecondCard', $requestDataProvider);
            }

        } catch (\MundiAPILib\Exceptions\ErrorException $error) {
            $this->logger->logger($error);
            throw new \InvalidArgumentException($error);
        } catch (\Exception $ex) {
            $this->logger->logger($ex);
            throw new \InvalidArgumentException($ex->getMessage());
        }

        return $response;

    }

    /**
     * @return \MundiAPILib\Models\CreateTokenRequest
     */
    public function getCardRequest()
    {
        return new \MundiAPILib\Models\CreateCardRequest();
    }

    protected function setCardToken($payment, $customer, $quote, $card, $requestDataProvider)
    {
        $request = $this->getCardRequest();

        if (!empty($card) && $card == 'FirstCard') {
            $request->number = $payment->getCcNumberFirst();
            $request->holderName = $payment->getCcOwnerFirst();
            $request->expMonth = $payment->getCcExpMonthFirst();
            $request->expYear = $payment->getCcExpYearFirst();
            $request->cvv = $payment->getCcCidFirst();
        }

        if (!empty($card) && $card == 'SecondCard') {
            $request->number = $payment->getCcNumberSecond();
            $request->holderName = $payment->getCcOwnerSecond();
            $request->expMonth = $payment->getCcExpMonthSecond();
            $request->expYear = $payment->getCcExpYearSecond();
            $request->cvv = $payment->getCcCidSecond();
        }

        $request->billingAddress = [
            'street' => $requestDataProvider->getCustomerAddressStreet(self::BILLING),
            'number' => $requestDataProvider->getCustomerAddressNumber(self::BILLING),
            'complement' => $requestDataProvider->getCustomerAddressComplement(self::BILLING),
            'zip_code' => trim(str_replace('-','',$quote->getBillingAddress()->getPostCode())),
            'neighborhood' => $requestDataProvider->getCustomerAddressDistrict(self::BILLING),
            'city' => $quote->getBillingAddress()->getCity(),
            'state' => $quote->getBillingAddress()->getRegionCode(),
            'country' => $quote->getBillingAddress()->getCountryId()
        ];
        $request->options = [
            'verify_card' => true
        ];

        $request->metadata = [
            'module_name' => self::NAME_METADATA,
            'module_version' => $this->getModuleHelper()->getVersion(self::MODULE_NAME),
        ];

        $result = $this->createCard($customer, $request);

        $this->setCard($quote, $customer, $payment, $result, $card);


        return $this;
    }

    protected function setCard($quote, $customer, $payment, $result, $card)
    {
        try {
            $cards = $this->getCardsFactory();
            $cards->setCustomerId($quote->getCustomerId());
            $cards->setCardToken($result->id);
            $cards->setCardId($customer->id);
            if($card == 'FirstCard'){
                $cards->setLastFourNumbers(substr($payment->getCcNumberFirst(), -4));
                $cards->setBrand($payment->getCcTypeFirst());
            }
            if($card == 'SecondCard'){
                $cards->setLastFourNumbers(substr($payment->getCcNumberSecond(), -4));
                $cards->setBrand($payment->getCcTypeSecond());
            }
            $cards->setCreatedAt(date("Y-m-d H:i:s"));
            $cards->setUpdatedAt(date("Y-m-d H:i:s"));
            $cards->save();
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException($ex->getMessage());
        }

        return $this;
    }

    protected function createCard($customer, $request)
    {
        try {
            $result = $this->getApi()->getCustomers()->createCard($customer->id, $request);
        } catch (\MundiAPILib\Exceptions\ErrorException $error) {
            throw new \InvalidArgumentException($error);
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException($ex->getMessage());
        }

        return $result;
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
