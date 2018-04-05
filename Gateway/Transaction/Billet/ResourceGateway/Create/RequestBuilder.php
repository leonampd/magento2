<?php
/**
 * Class RequestBuilder
 */

namespace PagarMe\Magento2\Gateway\Transaction\Billet\ResourceGateway\Create;

use function Couchbase\defaultDecoder;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Item;
use PagarMe\Magento2\Api\BilletRequestDataProviderInterface;
use PagarMe\Magento2\Api\BilletRequestDataProviderInterfaceFactory;
use PagarMe\Magento2\Api\CartItemRequestDataProviderInterface;
use PagarMe\Magento2\Api\CartItemRequestDataProviderInterfaceFactory;
use Magento\Checkout\Model\Cart;
use PagarMe\Magento2\Gateway\Transaction\Base\Config\Config;
use PagarMe\Magento2\Helper\ModuleHelper;
use PagarMe\Magento2\Model\Source\Bank;
use PagarMe\Magento2\Helper\Logger;

class RequestBuilder implements BuilderInterface
{

    const MODULE_NAME = 'PagarMe_Magento2';
    const NAME_METADATA = 'Magento 2';
    const SHIPPING = 1;
    const BILLING = 0;
    
    protected $request;
    /** @var  BoletoTransaction */
    protected $transaction;
    protected $requestDataProviderFactory;
    protected $cartItemRequestDataProviderFactory;
    protected $orderAdapter;
    protected $cart;
    protected $config;
    protected $moduleHelper;
    protected $bank;
    protected $paymentData;

    /**
     * @var \PagarMe\Magento2\Helper\Logger
     */
    protected $logger;

    /**
     * RequestBuilder constructor.
     * @param BilletRequestDataProviderInterfaceFactory $requestDataProviderFactory
     * @param CartItemRequestDataProviderInterfaceFactory $cartItemRequestDataProviderFactory
     * @param Cart $cart
     * @param Config $config
     * @param ModuleHelper $moduleHelper
     * @param Bank $bank
     */
    public function __construct(
        BilletRequestDataProviderInterfaceFactory $requestDataProviderFactory,
        CartItemRequestDataProviderInterfaceFactory $cartItemRequestDataProviderFactory,
        Cart $cart,
        Config $config,
        ModuleHelper $moduleHelper,
        Bank $bank,
        Logger $logger
    )
    {
        $this->setRequestDataProviderFactory($requestDataProviderFactory);
        $this->setCartItemRequestProviderFactory($cartItemRequestDataProviderFactory);
        $this->setCart($cart);
        $this->setConfig($config);
        $this->setModuleHelper($moduleHelper);
        $this->setBank($bank);
        $this->setLogger($logger);
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
     * @return BilletRequestDataProviderInterface
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
     * @param $requestDataProvider
     * @return mixed
     */
    protected function createNewRequest($requestDataProvider)
    {

        $quote = $this->getCart()->getQuote();
        $order = $this->getOrderRequest();
        $quote->reserveOrderId()->save();
        $order->code = $quote->getReservedOrderId();
        $order->payments = [
            [
                'amount' => $quote->getGrandTotal() * 100,
                'payment_method' => 'boleto',
                'capture' => false,
                'boleto' => [
                    'bank' => $this->getBank()->getBankNumber($requestDataProvider->getBankType()),
                    'instructions' => $requestDataProvider->getInstructions(),
                    'due_at' => $this->calcBoletoDays($requestDataProvider->getDaysToAddInBoletoExpirationDate())
                ]
            ]
        ];

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
            'name' => !empty($requestDataProvider->getName()) ? $requestDataProvider->getName() :  $quote->getBillingAddress()->getFirstName() . ' ' . $quote->getBillingAddress()->getLastName(),
            'email' => !empty($requestDataProvider->getEmail()) ? $requestDataProvider->getEmail() : $quote->getBillingAddress()->getEmail(),
            'document' => $document,
            'type' => 'individual',
            'address' => [
                'street' => $requestDataProvider->getCustomerAddressStreet(self::BILLING),
                'number' => $requestDataProvider->getCustomerAddressNumber(self::BILLING),
                'complement' => $requestDataProvider->getCustomerAddressComplement(self::BILLING),
                'zip_code' => trim(str_replace('-','',$quote->getBillingAddress()->getPostCode())),
                'neighborhood' => $requestDataProvider->getCustomerAddressDistrict(self::BILLING),
                'city' => $quote->getBillingAddress()->getCity(),
                'state' => $quote->getBillingAddress()->getRegionCode(),
                'country' => $quote->getBillingAddress()->getCountryId()
            ]
        ];

        $order->ip = $requestDataProvider->getIpAddress();

        $order->shipping = [
            'amount' => $quote->getShippingAddress()->getShippingAmount() * 100,
            'description' => '.',
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

        try {
            $this->getLogger()->logger($order->jsonSerialize());
            $response = $this->getApi()->getOrders()->createOrder($order);
        } catch (\MundiAPILib\Exceptions\ErrorException $error) {
            $this->getLogger()->logger($error);
            throw new \InvalidArgumentException($error);

            return $error;

        } catch (\Exception $ex) {
            $this->getLogger()->logger($ex);
            throw new \InvalidArgumentException($ex->getMessage());

            return $ex;
        }

        return $response;

    }

    /**
     * @return BoletoTransaction
     */
    protected function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param BoletoTransaction $transaction
     * @return RequestBuilder
     */
    protected function setTransaction(BoletoTransaction $transaction)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * @return BilletRequestDataProviderInterfaceFactory
     */
    protected function getRequestDataProviderFactory()
    {
        return $this->requestDataProviderFactory;
    }

    /**
     * @param BilletRequestDataProviderInterfaceFactory $requestDataProviderFactory
     * @return self13
     */
    protected function setRequestDataProviderFactory(BilletRequestDataProviderInterfaceFactory $requestDataProviderFactory)
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

    public function calcBoletoDays($days)
    {

        $pattern = 'T00:00:00Z';

        return date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $days . ' days')) . $pattern;

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
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;

    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
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
     * @return Bank
     */
    protected function getBank()
    {
        return $this->bank;
    }

    /**
     * @param Bank $bank
     */
    protected function setBank($bank)
    {
        $this->bank = $bank;
    }



    /**
     * @return mixed
     */
    public function getCartItemRequestDataProviderFactory()
    {
        return $this->cartItemRequestDataProviderFactory;
    }

    /**
     * @param mixed $cartItemRequestDataProviderFactory
     *
     * @return self
     */
    public function setCartItemRequestDataProviderFactory($cartItemRequestDataProviderFactory)
    {
        $this->cartItemRequestDataProviderFactory = $cartItemRequestDataProviderFactory;

        return $this;
    }

    /**
     * @return \PagarMe\Magento2\Helper\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param \PagarMe\Magento2\Helper\Logger $logger
     *
     * @return self
     */
    public function setLogger(\PagarMe\Magento2\Helper\Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
