<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogInventory\Test\Unit\Model;

use Magento\CatalogInventory\Model\AddStockStatusToCollection;

class AddStockStatusToCollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AddStockStatusToCollection
     */
    protected $plugin;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockHelper;

    protected function setUp()
    {
        $this->stockHelper = $this->createMock(\Magento\CatalogInventory\Helper\Stock::class);
        $this->plugin = (new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this))->getObject(
            \Magento\CatalogInventory\Model\AddStockStatusToCollection::class,
            [
                'stockHelper' => $this->stockHelper,
            ]
        );
    }

    public function testAddStockStatusToCollection()
    {
        $productCollection = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Product\Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockHelper->expects($this->once())
            ->method('addIsInStockFilterToCollection')
            ->with($productCollection)
            ->will($this->returnSelf());

        $this->plugin->beforeLoad($productCollection);
    }
}
