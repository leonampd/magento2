<?php

trait CustomerConfigProvider
{
    /**
     * @return array
     */
    public function getAddressConfigs()
    {
        $customerAddressConfig = [
            'payment/mundipagg_customer_address/street_attribute' => 'street_1',
            'payment/mundipagg_customer_address/number_attribute' => 'street_2',
            'payment/mundipagg_customer_address/district_attribute' => 'street_3'
            'payment/mundipagg_customer_address/complement_attribute' => 'street_4',
        ];

        return $customerAddressConfig;
    }
}
