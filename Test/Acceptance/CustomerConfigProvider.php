<?php

trait CustomerConfigProvider
{
    /**
     * @return array
     */
    public function getAddressConfigs()
    {
        $customerAddressConfig = [
            'payment/pagarme_customer_address/street_attribute' => 'street_1',
            'payment/pagarme_customer_address/number_attribute' => 'street_2',
            'payment/pagarme_customer_address/district_attribute' => 'street_3',
            'payment/pagarme_customer_address/complement_attribute' => 'street_4',
        ];

        return $customerAddressConfig;
    }
}
