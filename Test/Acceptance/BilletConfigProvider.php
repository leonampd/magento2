<?php

trait BilletConfigProvider
{
    /**
     * @return array
     */
    public function getBilletConfig()
    {
        $moduleBilletConfigs = [
            'payment/mundipagg_billet/types' => 'Bradesco',
        ];

        return $moduleBilletConfigs;
    }
}
