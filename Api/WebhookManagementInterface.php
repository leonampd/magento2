<?php

namespace PagarMe\Magento2\Api;

interface WebhookManagementInterface
{
    /**
     * @api
     * @param mixed $data
     * @return boolean
     */
    public function save($data);
}
