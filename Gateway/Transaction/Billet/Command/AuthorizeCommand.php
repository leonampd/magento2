<?php
/**
 * Class AuthorizeCommand
 */

namespace PagarMe\Magento2\Gateway\Transaction\Billet\Command;


use PagarMe\Magento2\Gateway\Transaction\Base\Command\AbstractApiCommand;

use MundiAPILib\Models\CreateOrderRequest;

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
