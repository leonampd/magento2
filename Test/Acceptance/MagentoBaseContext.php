<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
/**
 * Defines application features from the specific context.
 */
class MagentoBaseContext extends RawMinkContext
{
    use Page;
    use EnvironmentVariable;
    use SessionWait;

    /**
     * @var string $magentoUrl
     */
    private $magentoUrl;
    
    /**
     * @Given a magento ecommerce
     */
    public function aMagentoEcommerce()
    {
        $this->magentoUrl = $this->getEnvFromName('MAGENTO_URL');
    }

    /**
     * @When I access its  homepage
     */
    public function iAccessItsHomepage()
    {
        $this->getSession()->visit($this->magentoUrl);
    }

    /**
     * @Then I should see the ecommerce logo
     */
    public function iShouldSeeTheEcommerceLogo()
    {
        $page = $this->getPageFromSession();
        $this->spin(function($context) use ($page) {
            return ($page->find(
                'css',
                '.logo'
            )->isVisible());
        });
        $logo = $page->find('css', '.logo');
        $logo->isVisible();
    }

    /**
     * @Then a products list
     */
    public function aProductsList()
    {
        $page = $this->getPageFromSession();
        
        $this->spin(function() use($page) {
            $products = $page->findAll('css', '.product-item');
            if (count($products) <= 0) {
                throw new \Exception('There\'s no product on the page');
            }

            return $products;
        });
    }
}
