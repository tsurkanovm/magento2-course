<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Wishlist\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;

class WishlistTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Wishlist
     */
    private $wishlist;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
        $this->wishlist = $this->objectManager->get(Wishlist::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testAddNewItem()
    {
        $productSku = 'simple';
        $customerId = 1;
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $product = $productRepository->get($productSku);
        $this->wishlist->loadByCustomerId($customerId, true);
        $this->wishlist->addNewItem(
            $product,
            '{"qty":2}'
        );
        $this->wishlist->addNewItem(
            $product,
            ['qty' => 3]
        );
        $this->wishlist->addNewItem(
            $product,
            new DataObject(['qty' => 4])
        );
        $this->wishlist->addNewItem($product);
        /** @var Item $wishlistItem */
        $wishlistItem = $this->wishlist->getItemCollection()->getFirstItem();
        $this->assertInstanceOf(Item::class, $wishlistItem);
        $this->assertEquals($wishlistItem->getQty(), 10);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid wishlist item configuration.
     */
    public function testAddNewItemInvalidWishlistItemConfiguration()
    {
        $productSku = 'simple';
        $customerId = 1;
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $product = $productRepository->get($productSku);
        $this->wishlist->loadByCustomerId($customerId, true);
        $this->wishlist->addNewItem(
            $product,
            '{"qty":2'
        );
        $this->wishlist->addNewItem($product);
    }
}
