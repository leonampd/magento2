<?php

trait BilletConfigProvider
{
    /**
     * @return array
     */
    public function getBilletConfig()
    {
        $moduleBilletConfigs = [
            'payment/pagarme_billet/types' => 'Bradesco',
        ];

        return $moduleBilletConfigs;
    }
}
