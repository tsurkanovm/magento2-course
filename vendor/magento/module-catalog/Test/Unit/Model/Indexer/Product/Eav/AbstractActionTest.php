<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Test\Unit\Model\Indexer\Product\Eav;

class AbstractActionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Eav\AbstractAction|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eavDecimalFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eavSourceFactoryMock;

    protected function setUp()
    {
        $this->_eavDecimalFactoryMock = $this->createPartialMock(
            \Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\DecimalFactory::class,
            ['create']
        );
        $this->_eavSourceFactoryMock = $this->createPartialMock(
            \Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\SourceFactory::class,
            ['create']
        );
        $this->_model = $this->getMockForAbstractClass(
            \Magento\Catalog\Model\Indexer\Product\Eav\AbstractAction::class,
            [$this->_eavDecimalFactoryMock, $this->_eavSourceFactoryMock, []]
        );
    }

    public function testGetIndexers()
    {
        $expectedIndexers = [
            'source' => 'source_instance',
            'decimal' => 'decimal_instance',
        ];

        $this->_eavSourceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($expectedIndexers['source']));

        $this->_eavDecimalFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($expectedIndexers['decimal']));

        $this->assertEquals($expectedIndexers, $this->_model->getIndexers());
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Unknown EAV indexer type "unknown_type".
     */
    public function testGetIndexerWithUnknownTypeThrowsException()
    {
        $this->_eavSourceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('return_value'));

        $this->_eavDecimalFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('return_value'));

        $this->_model->getIndexer('unknown_type');
    }

    public function testGetIndexer()
    {
        $this->_eavSourceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('source_return_value'));

        $this->_eavDecimalFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('decimal_return_value'));

        $this->assertEquals('source_return_value', $this->_model->getIndexer('source'));
    }

    public function testReindexWithoutArgumentsExecutesReindexAll()
    {
        $eavSource = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\Source::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eavDecimal = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\Decimal::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eavDecimal->expects($this->once())
            ->method('reindexAll');

        $eavSource->expects($this->once())
            ->method('reindexAll');

        $this->_eavSourceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($eavSource));

        $this->_eavDecimalFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($eavDecimal));

        $this->_model->reindex();
    }

    public function testReindexWithNotNullArgumentExecutesReindexEntities()
    {
        $childIds = [1, 2, 3];
        $parentIds = [4];
        $reindexIds = array_merge($childIds, $parentIds);
        $connectionMock = $this->getMockBuilder(\Magento\Framework\DB\Adapter\AdapterInterface::class)
            ->getMockForAbstractClass();

        $eavSource = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\Source::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eavDecimal = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\Decimal::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eavSource->expects($this->once())->method('getRelationsByChild')->with($childIds)->willReturn($childIds);
        $eavSource->expects($this->once())->method('getRelationsByParent')->with($childIds)->willReturn($parentIds);

        $eavDecimal->expects($this->once())->method('getRelationsByChild')->with($reindexIds)->willReturn($reindexIds);
        $eavDecimal->expects($this->once())->method('getRelationsByParent')->with($reindexIds)->willReturn([]);

        $eavSource->expects($this->once())->method('getConnection')->willReturn($connectionMock);
        $eavDecimal->expects($this->once())->method('getConnection')->willReturn($connectionMock);
        $eavDecimal->expects($this->once())
            ->method('reindexEntities')
            ->with($reindexIds);

        $eavSource->expects($this->once())
            ->method('reindexEntities')
            ->with($reindexIds);

        $this->_eavSourceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($eavSource));

        $this->_eavDecimalFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($eavDecimal));

        $this->_model->reindex($childIds);
    }
}
