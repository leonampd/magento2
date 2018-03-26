<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;

abstract class PaymentContext extends RawMinkContext
{
    use CustomerConfigProvider;
    use ModuleConfigProvider;
    use InstallmentsConfigProvider;
    use SessionWait;

    protected $customer;
    protected $page;

    /**
     * @beforeSuite
     */
    abstract public static function setUp(BeforeSuiteScope $suiteScope);

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

        try {
            $this->page->pressButton('Sign In');
        } catch(\Exception $exception) {
            $this->spin(
                function($context) use ($page) {
                    $loader = $page->find('css', '.checkout-loader');
                    if(is_null($loader)) {
                        return true;
                    }

                    return false;
                }
            , 120);
            $this->page->pressButton('Sign In');
        } 
    }

    /**
     * @When login with registered user
     */
    public function loginWithRegisteredUser()
    {
        $page = $this->page;
        
        $this->spin(
            function($context) use($page) {
                return (
                    $page->find('css', '.authentication-dropdown')->isVisible() &&
                    $page->find('css', '#login-email')->isVisible() &&
                    $page->find('css', '#login-password')
                );
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
     * @When choose pay with transparent checkout :paymentMethod
     */
    public function choosePayWithTransparentCheckout($paymentMethod)
    {
        $page = $this->page;

        $this->spin(
            function($context) use($page, $paymentMethod) {
                return ($page->find('css', '#mundipagg_'.$paymentMethod));
            }
        );

        $this->getSession()->wait(2000);

        $this->page->find(
            'css',
            '#mundipagg_'.$paymentMethod
        )->selectOption('mundipagg_'.$paymentMethod);
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
