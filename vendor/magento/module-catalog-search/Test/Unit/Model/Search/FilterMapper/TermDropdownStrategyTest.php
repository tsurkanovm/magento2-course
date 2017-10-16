<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Test\Unit\Model\Search\FilterMapper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\DB\Select;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Class TermDropdownStrategyTest.
 * Unit test for \Magento\CatalogSearch\Model\Search\FilterMapper\TermDropdownStrategy.
 */
class TermDropdownStrategyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $eavConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfig;

    /**
     * @var \Magento\CatalogSearch\Model\Search\FilterMapper\TermDropdownStrategy
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->eavConfig = $this->getMockBuilder(EavConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->scopeConfig = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceMock = $this->getMockBuilder(\Magento\Framework\App\ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            \Magento\CatalogSearch\Model\Search\FilterMapper\TermDropdownStrategy::class,
            [
                'storeManager' => $this->storeManager,
                'scopeConfig' => $this->scopeConfig,
                'eavConfig' => $this->eavConfig,
                'resourceConnection' => $this->resourceMock
            ]
        );
    }

    public function testApply()
    {
        $searchFilter = $this->getMockBuilder(FilterInterface::class)
            ->setMethods(['getField', 'getType', 'getName'])
            ->getMock();
        $select = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();
        $attribute = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $store = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceMock->expects($this->any())
            ->method('getTableName')
            ->willReturn('cataloginventory_stock_status');
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->willReturn(false);
        $this->eavConfig->expects($this->once())
            ->method('getAttribute')
            ->willReturn($attribute);
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($store);
        $store->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $attribute->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $searchFilter->expects($this->once())
            ->method('getField')
            ->willReturn('filed');

        $this->assertTrue($this->model->apply($searchFilter, $select));
    }
}
