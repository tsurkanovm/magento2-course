<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRule\Test\Unit\Model\Indexer;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class IndexBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\CatalogRule\Model\Indexer\IndexBuilder
     */
    protected $indexBuilder;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateFormat;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateTime;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connection;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataPool;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $select;

    /**
     * @var \Zend_Db_Statement_Interface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $db;

    /**
     * @var \Magento\Store\Model\Website|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $website;

    /**
     * @var \Magento\Rule\Model\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $combine;

    /**
     * @var \Magento\CatalogRule\Model\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rules;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attribute;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backend;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $reindexRuleProductPrice;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $reindexRuleGroupWebsite;

    /**
     * Set up test
     *
     * @return void
     */
    protected function setUp()
    {
        $this->resource = $this->createPartialMock(
            \Magento\Framework\App\ResourceConnection::class,
            ['getConnection', 'getTableName']
        );
        $this->ruleCollectionFactory = $this->createPartialMock(
            \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory::class,
            ['create', 'addFieldToFilter']
        );
        $this->backend = $this->createMock(\Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend::class);
        $this->select = $this->createMock(\Magento\Framework\DB\Select::class);
        $this->metadataPool = $this->createMock(\Magento\Framework\EntityManager\MetadataPool::class);
        $metadata = $this->getMockBuilder(\Magento\Framework\EntityManager\EntityMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->metadataPool->expects($this->any())->method('getMetadata')->willReturn($metadata);
        $this->connection = $this->createMock(\Magento\Framework\DB\Adapter\AdapterInterface::class);
        $this->db = $this->createMock(\Zend_Db_Statement_Interface::class);
        $this->website = $this->createMock(\Magento\Store\Model\Website::class);
        $this->storeManager = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $this->combine = $this->createMock(\Magento\Rule\Model\Condition\Combine::class);
        $this->rules = $this->createMock(\Magento\CatalogRule\Model\Rule::class);
        $this->logger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $this->attribute = $this->createMock(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute::class);
        $this->priceCurrency = $this->createMock(\Magento\Framework\Pricing\PriceCurrencyInterface::class);
        $this->dateFormat = $this->createMock(\Magento\Framework\Stdlib\DateTime::class);
        $this->dateTime = $this->createMock(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        $this->eavConfig = $this->createPartialMock(\Magento\Eav\Model\Config::class, ['getAttribute']);
        $this->product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $this->productFactory = $this->createPartialMock(\Magento\Catalog\Model\ProductFactory::class, ['create']);
        $this->connection->expects($this->any())->method('select')->will($this->returnValue($this->select));
        $this->connection->expects($this->any())->method('query')->will($this->returnValue($this->db));
        $this->select->expects($this->any())->method('distinct')->will($this->returnSelf());
        $this->select->expects($this->any())->method('where')->will($this->returnSelf());
        $this->select->expects($this->any())->method('from')->will($this->returnSelf());
        $this->select->expects($this->any())->method('order')->will($this->returnSelf());
        $this->resource->expects($this->any())->method('getConnection')->will($this->returnValue($this->connection));
        $this->resource->expects($this->any())->method('getTableName')->will($this->returnArgument(0));
        $this->storeManager->expects($this->any())->method('getWebsites')->will($this->returnValue([$this->website]));
        $this->storeManager->expects($this->any())->method('getWebsite')->will($this->returnValue($this->website));
        $this->rules->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->rules->expects($this->any())->method('getWebsiteIds')->will($this->returnValue([1]));
        $this->rules->expects($this->any())->method('getCustomerGroupIds')->will($this->returnValue([1]));
        $this->ruleCollectionFactory->expects($this->any())->method('create')->will($this->returnSelf());
        $this->ruleCollectionFactory->expects($this->any())->method('addFieldToFilter')->will(
            $this->returnValue([$this->rules])
        );
        $this->product->expects($this->any())->method('load')->will($this->returnSelf());
        $this->product->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->product->expects($this->any())->method('getWebsiteIds')->will($this->returnValue([1]));
        $this->rules->expects($this->any())->method('validate')->with($this->product)->willReturn(true);
        $this->attribute->expects($this->any())->method('getBackend')->will($this->returnValue($this->backend));
        $this->productFactory->expects($this->any())->method('create')->will($this->returnValue($this->product));

        $this->indexBuilder = new \Magento\CatalogRule\Model\Indexer\IndexBuilder(
            $this->ruleCollectionFactory,
            $this->priceCurrency,
            $this->resource,
            $this->storeManager,
            $this->logger,
            $this->eavConfig,
            $this->dateFormat,
            $this->dateTime,
            $this->productFactory
        );
        $this->reindexRuleProductPrice =
            $this->getMockBuilder(\Magento\CatalogRule\Model\Indexer\ReindexRuleProductPrice::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->reindexRuleGroupWebsite =
            $this->getMockBuilder(\Magento\CatalogRule\Model\Indexer\ReindexRuleGroupWebsite::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->setProperties($this->indexBuilder, [
            'metadataPool' => $this->metadataPool,
            'reindexRuleProductPrice' => $this->reindexRuleProductPrice,
            'reindexRuleGroupWebsite' => $this->reindexRuleGroupWebsite
        ]);
    }

    /**
     * Test UpdateCatalogRuleGroupWebsiteData
     *
     * @covers \Magento\CatalogRule\Model\Indexer\IndexBuilder::updateCatalogRuleGroupWebsiteData
     * @return void
     */
    public function testUpdateCatalogRuleGroupWebsiteData()
    {
        $priceAttrMock = $this->createPartialMock(\Magento\Catalog\Model\Entity\Attribute::class, ['getBackend']);
        $backendModelMock = $this->createPartialMock(
            \Magento\Catalog\Model\Product\Attribute\Backend\Tierprice::class,
            ['getResource']
        );
        $resourceMock = $this->createPartialMock(
            \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice::class,
            ['getMainTable']
        );
        $resourceMock->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue('catalog_product_entity_tear_price'));
        $backendModelMock->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($resourceMock));
        $priceAttrMock->expects($this->any())
            ->method('getBackend')
            ->will($this->returnValue($backendModelMock));

        $this->reindexRuleProductPrice->expects($this->once())->method('execute')->willReturn(true);
        $this->reindexRuleGroupWebsite->expects($this->once())->method('execute')->willReturn(true);

        $this->indexBuilder->reindexByIds([1]);
    }

    /**
     * @param $object
     * @param array $properties
     */
    private function setProperties($object, $properties = [])
    {
        $reflectionClass = new \ReflectionClass(get_class($object));
        foreach ($properties as $key => $value) {
            if ($reflectionClass->hasProperty($key)) {
                $reflectionProperty = $reflectionClass->getProperty($key);
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($object, $value);
            }
        }
    }
}
