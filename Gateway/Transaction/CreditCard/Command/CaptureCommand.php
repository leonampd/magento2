<?php
/**
 * Class CaptureCommand
 */

namespace PagarMe\Magento2\Gateway\Transaction\CreditCard\Command;

use PagarMe\Magento2\Gateway\Transaction\Base\Command\AbstractApiCommand;

class CaptureCommand extends AbstractApiCommand
{
    /**
     * @param $request
     * @return mixed
     */
    protected function sendRequest($request)
    {
        if (!isset($request)) {
            throw new \InvalidArgumentException('PagarMe Request object should be provided');
        }
        return $request;
    }

}


