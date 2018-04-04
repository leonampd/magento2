<?php
/**
 * Class GeneralHandler
 */

namespace PagarMe\Magento2\Gateway\Transaction\BilletCreditCard\ResourceGateway\Create\Response;


use Magento\Payment\Gateway\Response\HandlerInterface;
use PagarMe\Magento2\Gateway\Transaction\Base\ResourceGateway\Response\AbstractHandler;
use PagarMe\Magento2\Model\ChargesFactory;

class GeneralHandler extends AbstractHandler implements HandlerInterface
{
	/**
     * \PagarMe\Magento2\Model\ChargesFactory
     */
	protected $modelCharges;

	/**
     * @return void
     */
    public function __construct(
    	ChargesFactory $modelCharges
    ) {
        $this->modelCharges = $modelCharges;
    }

    /**
     * {@inheritdoc}
     */
    protected function _handle($payment, $response)
    {

        $boletoUrl = '';
        foreach ($response->charges as $charge){
            if($charge->paymentMethod ==  'boleto'){
                $boletoUrl = $charge->lastTransaction->pdf;
            }
        }

        $payment->setAdditionalInformation('billet_url', $boletoUrl);

        $payment->setTransactionId($response->id);
        $payment->setIsTransactionClosed(false);

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
