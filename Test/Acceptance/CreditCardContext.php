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
    private $page;

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
        $this->page = $this->getSession()->getPage();
        $this->page->pressButton("Add to Cart");
    }

    /**
     * @When I go to checkout page
     */
    public function iGoToCheckoutPage()
    {
        $this->getSession()->visit(
            $this->locatePath('/checkout')
        );
        $page = $this->page;
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
        $page = $this->page;
        
        $this->spin(
            function($context) use($page) {
                return ($page->find('css', '.authentication-dropdown')->isVisible());
            }
        );
        
        $this->page->fillField(
            'username',
            $this->customer->username
        );

        $this->page->fillField(
            'password',
            $this->customer->password
        );

        $this->page->find('css', '.action-login.secondary')->click();

        $this->spin(
            function($context) use($page) {
                return ($page->find('css', '.shipping-address-item'));
            }
        );

        $shippingAddres = $page->find('css', '.shipping-address-item')->getText();

        if(strpos($shippingAddres, $this->customer->name) === false){
            throw new Exception();
        }

        $this->getSession()->wait(2000);
        $this->page->pressButton('Next');
    }

    /**
     * @When choose pay with transparent checkout using credit card
     */
    public function choosePayWithTransparentCheckoutUsingCreditCard()
    {
        $page = $this->page;

        $this->spin(
            function($context) use($page) {
                return ($page->find('css', '#mundipagg_creditcard'));
            }
        );

        $this->getSession()->wait(2000);

        $this->page->find(
            'css',
            '#mundipagg_creditcard'
        )->selectOption('mundipagg_creditcard');
    }

    /**
     * @When I confirm my payment information
     */
    public function iConfirmMyPaymentInformation()
    {
        $this->page->find(
            'css',
            '#mundipagg_creditcard_cc_number'
        )->setValue('4242424242424242');

        $this->page->find(
            'css',
            '#mundipagg_creditcard_cc_owner'
        )->setValue('Alan Turing');
        
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

    }

    /**
     * @When place order
     */
    public function placeOrder()
    {
        $page = $this->page;
        $this->spin(
            function($context) use($page) {
                return ($page->find(
                    'css',
                    '.payment-method._active button.primary.checkout'
                ));
            }
        );
        $this->page->find(
            'css',
            '.payment-method._active button.primary.checkout'
        )->click();
    }

    /**
     * @Then the purchase must be paid with success
     */
    public function thePurchaseMustBePaidWithSuccess()
    {
        $page = $this->page;
        $this->spin(
            function($context) use ($page) {
                $pageTitle = $this->page->find('css', '.page-title-wrapper')->getText();
                if(strpos($pageTitle, 'Thank you for your purchase!') === false) {
                    return false;
                }
                return true;
            }
        );
    }

    public function getCustomer()
    {
        $customer = new stdClass;
        $customer->name = 'Alan Turing';
        $customer->username = 'alan@turing.com';
        $customer->password = '##Abc123456##';

        return $customer;
    }
}
