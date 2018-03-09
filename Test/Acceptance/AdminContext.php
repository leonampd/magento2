<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class AdminContext extends RawMinkContext
{
    use EnvironmentVariable;

    private $adminUser;

    /**
     * @Given an registered admin
     */
    public function anRegisteredAdmin()
    {
        $this->adminUser = $this->getAdmin();
    }

    /**
     * @When I visit admin login page
     */
    public function iVisitAdminLoginPage()
    {
        $this->getSession()->visit(
            $this->locatePath('/admin')
        );
    }

    /**
     * @When I fill username input
     */
    public function iFillUsernameInput()
    {
        $this->getSession()->getPage()->fillField(
            'username',
            $this->adminUser->username
        );
    }

    /**
     * @When I fill the password input
     */
    public function iFillThePasswordInput()
    {
        $this->getSession()->getPage()->fillField(
            'password',
            $this->adminUser->password
        );
    }

    /**
     * @When I click on login button
     */
    public function iClickOnLoginButton()
    {
        $this->getSession()
            ->getPage()
            ->pressButton("Sign in");
    }

    /**
     * @Then I should see pagar.me as an item on left menu
     */
    public function iShouldSeePagarMeAsAnItemOnLeftMenu()
    {
        $page = $this->getSession()->getPage();
        
        $this->getSession()->wait(
            1000,
            "document.readyState === 'complete'"
        );
        
        $moduleItemMenuBar = $page->find(
            'css',
            $this->getModuleMenuBarId()
        );
        $moduleItemMenuBar->isVisible();
    }

    public function getAdmin()
    {
        $admin = new stdClass;
        $admin->username = $this->getEnvFromName('MAGENTO_ADMIN_USERNAME');
        $admin->password = $this->getEnvFromName('MAGENTO_ADMIN_PASSWORD');

        return $admin;
    }

    public function getModuleMenuBarId()
    {
        return '#menu-mundipagg-top-level';
    }
}
