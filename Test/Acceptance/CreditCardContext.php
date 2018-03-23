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
    use SessionWait;

    private $customer;
    
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
       $this->customer =  $this->getCustomer(); 
    }

    /**
     * @When I access the store page
     */
    public function iAccessTheStorePage()
    {
        $this->getSession()->visit(
            $this->locatePath('/')
        );
    }

    /**
     * @When add any product to basket
     */
    public function addAnyProductToBasket()
    {
        $this->getSession()
            ->getPage()
            ->pressButton("Add to Cart");
    }

    /**
     * @When I go to checkout page
     */
    public function iGoToCheckoutPage()
    {
        $this->getSession()->visit(
            $this->locatePath('/checkout')
        );

        $page = $this->getSession()->getPage();
        
        $this->spin(
            function($context) use($page) {
                return ($page->find('css', '.action-auth-toggle')->isVisible());
            }
        );
        
        $page->find('css', '.action-auth-toggle')->click();
    }

    /**
     * @When login with registered user
     */
    public function loginWithRegisteredUser()
    {
        $page = $this->getSession()->getPage();
        $page->fillField(
            'username',
            'alan@turing.com'
        );

        $page->fillField(
            'password',
            '##Abc123456##'
        );

        $page->find('css', '.action-login.secondary')->click();

        $this->spin(
            function($context) use($page) {
                $loadingMask = $page->find('css', '.loading-mask')->isVisible();
                return ($loadingMask == false) ? true : false;
            }

        );
        //$this->getSession()->wait(10000);
        $page->pressButton("Next");
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

    public function getCustomer()
    {
        $customer = new stdClass;
        $customer->username = 'alan@turing.com';
        $customer->password = '##Abc123456##';

        return $customer;
    }
}
