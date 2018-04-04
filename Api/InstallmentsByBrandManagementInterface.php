<?php

namespace PagarMe\Magento2\Api;

interface InstallmentsByBrandManagementInterface
{
    /**
     * @param mixed $brand
     * @return mixed
     */
    public function getInstallmentsByBrand($brand);

}
