<?php 

trait ModuleConfigProvider
{
    /**
     * @return array
     */
    public function getModuleConfigs()
    {
        $magentoConfigs = [
            'customer/address/street_lines' => 4, 
            'customer/create_account/vat_frontend_visibility' => 1,
            'customer/address/taxvat_show' => 'req'
        ];

        return $magentoConfigs;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getModuleKeys()
    {
        $publicKey = getenv('MODULE_PUBLIC_KEY');
        $secretKey = getenv('MODULE_SECRET_KEY');

        if (empty($secretKey) && empty($publicKey)) {
            throw new \Exception('You should inform your Public and Secret Keys');
        }

        $moduleKeys = [
            'pagarme_magento2/global/public_key' => $publicKey,
            'pagarme_magento2/global/secret_key' => $secretKey,
        ];

        return $moduleKeys;
    }
}
