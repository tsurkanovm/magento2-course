<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rule\Test\Unit\Model\ResourceModel\Rule\Collection;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class AbstractCollectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $abstractCollection;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\Data\Collection\EntityFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_entityFactoryMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loggerMock;

    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fetchStrategyMock;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_managerMock;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_db;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $selectMock;

    protected function setUp()
    {
        $this->_entityFactoryMock = $this->createMock(\Magento\Framework\Data\Collection\EntityFactoryInterface::class);
        $this->_loggerMock = $this->createMock(\Psr\Log\LoggerInterface::class);
        $this->_fetchStrategyMock = $this->createMock(
            \Magento\Framework\Data\Collection\Db\FetchStrategyInterface::class
        );
        $this->_managerMock = $this->createMock(\Magento\Framework\Event\ManagerInterface::class);
        $this->_db = $this->getMockForAbstractClass(
            \Magento\Framework\Model\ResourceModel\Db\AbstractDb::class,
            [],
            '',
            false,
            false,
            true,
            ['__sleep', '__wakeup', 'getTable']
        );
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->abstractCollection = $this->getMockForAbstractClass(
            \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection::class,
            [
                'entityFactory' => $this->_entityFactoryMock,
                'logger' => $this->_loggerMock,
                'fetchStrategy' => $this->_fetchStrategyMock,
                'eventManager' => $this->_managerMock,
                null,
                $this->_db
            ],
            '',
            false,
            false,
            true,
            ['__sleep', '__wakeup', '_getAssociatedEntityInfo', 'getConnection', 'getSelect', 'getTable']
        );
    }

    public function addWebsitesToResultDataProvider()
    {
        return [
            [null, true],
            [true, true],
            [false, false]
        ];
    }

    /**
     * @dataProvider addWebsitesToResultDataProvider
     */
    public function testAddWebsitesToResult($flag, $expectedResult)
    {
        $this->abstractCollection->addWebsitesToResult($flag);
        $this->assertEquals($expectedResult, $this->abstractCollection->getFlag('add_websites_to_result'));
    }

    protected function _prepareAddFilterStubs()
    {
        $entityInfo = [];
        $entityInfo['entity_id_field'] = 'entity_id';
        $entityInfo['rule_id_field'] = 'rule_id';
        $entityInfo['associations_table'] = 'assoc_table';

        $connection = $this->createMock(\Magento\Framework\DB\Adapter\AdapterInterface::class);
        $select = $this->createMock(\Magento\Framework\DB\Select::class);
        $collectionSelect = $this->createMock(\Magento\Framework\DB\Select::class);

        $connection->expects($this->any())
            ->method('select')
            ->will($this->returnValue($select));

        $select->expects($this->any())
            ->method('from')
            ->will($this->returnSelf());

        $select->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());

        $this->abstractCollection->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        $this->_db->expects($this->any())
            ->method('getTable')
            ->will($this->returnArgument(0));

        $this->abstractCollection->expects($this->any())
            ->method('getSelect')
            ->will($this->returnValue($collectionSelect));

        $this->abstractCollection->expects($this->any())
            ->method('_getAssociatedEntityInfo')
            ->will($this->returnValue($entityInfo));
    }

    public function testAddWebsiteFilter()
    {
        $this->_prepareAddFilterStubs();
        $website = $this->createPartialMock(\Magento\Store\Model\Website::class, ['getId', '__sleep', '__wakeup']);

        $website->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->assertInstanceOf(
            \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection::class,
            $this->abstractCollection->addWebsiteFilter($website)
        );
    }

    public function testAddWebsiteFilterArray()
    {
        $this->selectMock = $this->getMockBuilder(\Magento\Framework\DB\Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock = $this->getMockBuilder(\Magento\Framework\DB\Adapter\AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->connectionMock->expects($this->atLeastOnce())
            ->method('quoteInto')
            ->with($this->equalTo('website. IN (?)'), $this->equalTo(['2', '3']))
            ->willReturn(true);

        $this->abstractCollection->expects($this->atLeastOnce())->method('getSelect')->willReturn($this->selectMock);
        $this->abstractCollection->expects($this->atLeastOnce())->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->assertInstanceOf(
            \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection::class,
            $this->abstractCollection->addWebsiteFilter(['2', '3'])
        );
    }

    public function testAddFieldToFilter()
    {
        $this->_prepareAddFilterStubs();
        $result = $this->abstractCollection->addFieldToFilter('website_ids', []);
        $this->assertNotNull($result);
    }
}
