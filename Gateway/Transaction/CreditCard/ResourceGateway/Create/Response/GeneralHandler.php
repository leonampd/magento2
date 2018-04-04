<?php
/**
 * Class GeneralHandler
 */

namespace PagarMe\Magento2\Gateway\Transaction\CreditCard\ResourceGateway\Create\Response;


use Magento\Payment\Gateway\Response\HandlerInterface;
use PagarMe\Magento2\Gateway\Transaction\Base\ResourceGateway\Response\AbstractHandler;
use PagarMe\Magento2\Model\ChargesFactory;
use PagarMe\Magento2\Gateway\Transaction\CreditCard\Config\Config as ConfigCreditCard;
use PagarMe\Magento2\Helper\Logger;

class GeneralHandler extends AbstractHandler implements HandlerInterface
{
	/**
     * \PagarMe\Magento2\Model\ChargesFactory
     */
	protected $modelCharges;

    /**
     * \PagarMe\Magento2\Gateway\Transaction\CreditCard\Config\Config
     */
    protected $configCreditCard;

    /**
     * @var \PagarMe\Magento2\Helper\Logger
     */
    protected $logger;

    public function __construct(
        ConfigCreditCard $configCreditCard,
    	ChargesFactory $modelCharges,
        Logger $logger
    ) {
        $this->modelCharges = $modelCharges;
        $this->configCreditCard = $configCreditCard;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function _handle($payment, $response)
    {
        $this->logger->logger(json_encode($response));
        $payment->setTransactionId($response->id);

        if($this->configCreditCard->getPaymentAction() == 'authorize_capture')
        {
            $payment->setIsTransactionClosed(true);
            $payment->accept()
                ->setParentTransactionId($response->id);
        }else{
            $payment->setIsTransactionClosed(false);
        }

        foreach($response->charges as $charge)
        {
        	try {
        		$model = $this->modelCharges->create();
	            $model->setChargeId($charge->id);
	            $model->setCode($charge->code);
	            $model->setOrderId($payment->getOrder()->getIncrementId());
	            $model->setType($charge->paymentMethod);
	            $model->setStatus($charge->status);
	            $model->setAmount($charge->amount);
                
	            $model->setPaidAmount(0);
	            $model->setRefundedAmount(0);
	            $model->setCreatedAt(date("Y-m-d H:i:s"));
	            $model->setUpdatedAt(date("Y-m-d H:i:s"));
	            $model->save();
        	} catch (\Exception $e) {
        		return $e->getMessage();
        	}
        }

        return $this;
    }
}
