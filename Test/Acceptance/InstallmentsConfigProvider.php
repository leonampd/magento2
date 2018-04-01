<?php

trait InstallmentsConfigProvider
{
    /**
     * @return array
     */
    public function getInstallmentsConfig()
    {
        $moduleInstallmentsConfigs = [
            'payment/pagarme_creditcard/installments_active' => 1,
            // installmentes confs should be valid to all brands?
            'payment/pagarme_creditcard/installments_type' => 1
        ];

        return $moduleInstallmentsConfigs;
    }
}
