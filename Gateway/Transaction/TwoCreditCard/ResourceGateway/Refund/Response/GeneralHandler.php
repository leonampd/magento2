<?php
/**
 * Class GeneralHandler
 */

namespace PagarMe\Magento2\Gateway\Transaction\TwoCreditCard\ResourceGateway\Refund\Response;


use Magento\Payment\Gateway\Response\HandlerInterface;
use PagarMe\Magento2\Gateway\Transaction\Base\ResourceGateway\Response\AbstractHandler;
use PagarMe\Magento2\Model\ChargesFactory;
use PagarMe\Magento2\Helper\Logger;

class GeneralHandler extends AbstractHandler implements HandlerInterface
{
	/**
     * \PagarMe\Magento2\Model\ChargesFactory
     */
	protected $modelCharges;

    /**
     * @var \PagarMe\Magento2\Helper\Logger
     */
    protected $logger;

	/**
     * @return void
     */
    public function __construct(
    	ChargesFactory $modelCharges,
        Logger $logger
    ) {
        $this->modelCharges = $modelCharges;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function _handle($payment, $response)
    {
        $this->logger->logger(json_encode($response));
        $model = $this->modelCharges->create();
        $charge = $model->getCollection()->addFieldToFilter('charge_id',array('eq' => $response->id))->getFirstItem();
        try {
            $charge->setStatus($response->status);
            $charge->setPaidAmount($response->amount);
            $charge->setUpdatedAt(date("Y-m-d H:i:s"));
            $charge->save();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
        return $this;
    }
}
