<?php
/**
 * Class Logger
 */

namespace PagarMe\Magento2\Helper;

class Logger
{
    /**
     * @param mixed $data
     */
    public function logger($data){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pagarme-' . date('Y-m-d') . '.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data);
    }
}
