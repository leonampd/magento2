<?php

use Behat\Testwork\Hook\Scope\BeforeSuiteScope;

class BilletContext extends PaymentContext
{
    use BilletConfigProvider;

    /**
     * @beforeSuite
     */
    public static function setUp(BeforeSuiteScope $suiteScope)
    {
        $configs = [];
        $magentoConfigs = self::getModuleConfigs();
        $customerAddressConfig = self::getAddressConfigs();
        $moduleKeys = self::getModuleKeys();
        $moduleBilletConfigs = self::getBilletConfig();

        $configs = array_merge(
            $magentoConfigs,
            $customerAddressConfig,
            $moduleKeys,
            $moduleBilletConfigs
        );

        foreach($configs as $configKey => $configValue) {
            $command = sprintf(
                'bin/magento config:set %s %s --lock',
                $configKey,
                $configValue
            );
            exec($command, $output, $exitCode);
            if ($exitCode != 0) {
                throw new \Exception($output, $exitCode);
            }
        }
    }

    public function aRegisteredUser()
    {
        parent::aRegisteredUser();
    }

    public function iAccessTheStorePage()
    {
        parent::iAccessTheStorePage();
    }

    public function addAnyProductToBasket()
    {
        parent::addAnyProductToBasket();
    }

    public function iGoToCheckoutPage()
    {
        parent::iGoToCheckoutPage();
    }

    public function loginWithRegisteredUser()
    {
        parent::loginWithRegisteredUser();
    }

    public function choosePayWithTransparentCheckout($paymentMethod)
    {
        parent::choosePayWithTransparentCheckout($paymentMethod);
    }


    public function placeOrder()
    {
        parent::placeOrder();
    }

    public function thePurchaseMustBePaidWithSuccess()
    {
        parent::thePurchaseMustBePaidWithSuccess();

        $this->page->find('css', '.action.tocart.primary')->isVisible();
    }
}
