<?php

use Behat\Testwork\Hook\Scope\BeforeSuiteScope;

class CreditCardContext extends PaymentContext
{

    /**
     * @beforeSuite
     */
    public static function setUp(BeforeSuiteScope $suiteScope)
    {
        $configs = [];
        $magentoConfigs = self::getModuleConfigs();
        $customerAddressConfig = self::getAddressConfigs();
        $moduleKeys = self::getModuleKeys();
        $moduleInstallmentsConfigs = self::getInstallmentsConfig();

        $configs = array_merge(
            $magentoConfigs,
            $customerAddressConfig,
            $moduleKeys,
            $moduleInstallmentsConfigs
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

    /**
     * @When I confirm my payment information with installment :installment
     */
    public function iConfirmMyPaymentInformationWithInstallment($installment)
    {
        $this->page->find(
            'css',
            '#mundipagg_creditcard_cc_number'
        )->setValue('4242424242424242');
        $this->page->find(
            'css',
            '#mundipagg_creditcard_cc_owner'
        )->setValue($this->customer->name);

        $this->page->find(
            'css',
            '#mundipagg_creditcard_expiration'
        )->selectOption('12');

        $this->page->find(
            'css',
            '#mundipagg_creditcard_expiration_yr'
        )->selectOption('2028');
        $this->page->find(
            'css',
            '#mundipagg_creditcard_cc_cid'
        )->setValue('123');
        $this->page->find(
            'css',
            '#mundipagg_creditcard_installments'
        )->selectOption($installment);
    }

    public function placeOrder()
    {
        parent::placeOrder();
    }

    public function thePurchaseMustBePaidWithSuccess()
    {
        parent::thePurchaseMustBePaidWithSuccess();
    }
}
