<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;

/**
 * Defines application features from the specific context.
 */
class CreditCardContext extends RawMinkContext
{
    use CustomerConfigProvider;
    use ModuleConfigProvider;
    use InstallmentsConfigProvider;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

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

    /**
     * @Given a registered user
     */
    public function aRegisteredUser()
    {
        throw new Exception();
    }

    /**
     * @When I access the store page
     */
    public function iAccessTheStorePage()
    {
        throw new Exception();
    }

    /**
     * @When add any product to basket
     */
    public function addAnyProductToBasket()
    {
        throw new Exception();
    }

    /**
     * @When I go to checkout page
     */
    public function iGoToCheckoutPage()
    {
        throw new Exception();
    }

    /**
     * @When login with registered user
     */
    public function loginWithRegisteredUser()
    {
        throw new Exception();
    }

    /**
     * @When confirm billing and shipping address information
     */
    public function confirmBillingAndShippingAddressInformation()
    {
        throw new Exception();
    }

    /**
     * @When choose pay with transparent checkout using credit card
     */
    public function choosePayWithTransparentCheckoutUsingCreditCard()
    {
        throw new Exception();
    }

    /**
     * @When I confirm my payment information
     */
    public function iConfirmMyPaymentInformation()
    {
        throw new Exception();
    }

    /**
     * @When place order
     */
    public function placeOrder()
    {
        throw new Exception();
    }

    /**
     * @Then the purchase must be paid with success
     */
    public function thePurchaseMustBePaidWithSuccess()
    {
        throw new Exception();
    }
}
