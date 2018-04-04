<?php
/**
 * Class AuthorizeCommand
 */

namespace PagarMe\Magento2\Gateway\Transaction\BilletCreditCard\Command;

use PagarMe\Magento2\Gateway\Transaction\Base\Command\AbstractApiCommand;

class AuthorizeCommand extends AbstractApiCommand
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


