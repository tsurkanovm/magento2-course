<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogSearch\Controller;

class ResultTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/query.php
     */
    public function testIndexActionTranslation()
    {
        $this->markTestSkipped('MAGETWO-44910');
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get(\Magento\Framework\Locale\ResolverInterface::class)->setLocale('de_DE');

        $this->getRequest()->setParam('q', 'query_text');
        $this->dispatch('catalogsearch/result');

        $responseBody = $this->getResponse()->getBody();
        $this->assertNotContains('for="search">Search', $responseBody);
        $this->assertStringMatchesFormat('%aSuche%S%a', $responseBody);

        $this->assertNotContains('Search entire store here...', $responseBody);
        $this->assertContains('Den gesamten Shop durchsuchen...', $responseBody);
    }

    public function testIndexActionXSSQueryVerification()
    {
        $this->getRequest()->setParam('q', '<script>alert(1)</script>');
        $this->dispatch('catalogsearch/result');

        $responseBody = $this->getResponse()->getBody();
        $data = '<script>alert(1)</script>';
        $this->assertNotContains($data, $responseBody);
        $this->assertContains(htmlspecialchars($data, ENT_COMPAT, 'UTF-8', false), $responseBody);
    }

    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/query_redirect.php
     */
    public function testRedirect()
    {
        $this->dispatch('/catalogsearch/result/?q=query_text');
        $responseBody = $this->getResponse();

        $this->assertTrue($responseBody->isRedirect());
    }

    /**
     * @magentoDataFixture Magento/CatalogSearch/_files/query_redirect.php
     */
    public function testNoRedirectIfCurrentUrlAndRedirectTermAreSame()
    {
        $this->dispatch('/catalogsearch/result/?q=query_text&cat=41');
        $responseBody = $this->getResponse();

        $this->assertFalse($responseBody->isRedirect());
    }
}
