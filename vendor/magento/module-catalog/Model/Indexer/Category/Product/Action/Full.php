<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Indexer\Category\Product\Action;

use Magento\Catalog\Model\ResourceModel\Indexer\ActiveTableSwitcher;
use Magento\Framework\DB\Query\Generator as QueryGenerator;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Full reindex action
 *
 * @package Magento\Catalog\Model\Indexer\Category\Product\Action
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Full extends \Magento\Catalog\Model\Indexer\Category\Product\AbstractAction
{
    /**
     * @var \Magento\Framework\Indexer\BatchSizeManagementInterface
     */
    private $batchSizeManagement;

    /**
     * @var \Magento\Framework\Indexer\BatchProviderInterface
     */
    private $batchProvider;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * Row count to process in a batch
     *
     * @var int
     */
    private $batchRowsCount;

    /**
     * @var ActiveTableSwitcher
     */
    private $activeTableSwitcher;

    /**
     * @param ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $config
     * @param QueryGenerator|null $queryGenerator
     * @param \Magento\Framework\Indexer\BatchSizeManagementInterface|null $batchSizeManagement
     * @param \Magento\Framework\Indexer\BatchProviderInterface|null $batchProvider
     * @param \Magento\Framework\EntityManager\MetadataPool|null $metadataPool
     * @param \Magento\Indexer\Model\Indexer\StateFactory|null $stateFactory
     * @param int|null $batchRowsCount
     * @param ActiveTableSwitcher|null $activeTableSwitcher
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $config,
        QueryGenerator $queryGenerator = null,
        \Magento\Framework\Indexer\BatchSizeManagementInterface $batchSizeManagement = null,
        \Magento\Framework\Indexer\BatchProviderInterface $batchProvider = null,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool = null,
        $batchRowsCount = null,
        ActiveTableSwitcher $activeTableSwitcher = null
    ) {
        parent::__construct(
            $resource,
            $storeManager,
            $config,
            $queryGenerator
        );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->batchSizeManagement = $batchSizeManagement ?: $objectManager->get(
            \Magento\Framework\Indexer\BatchSizeManagementInterface::class
        );
        $this->batchProvider = $batchProvider ?: $objectManager->get(
            \Magento\Framework\Indexer\BatchProviderInterface::class
        );
        $this->metadataPool = $metadataPool ?: $objectManager->get(
            \Magento\Framework\EntityManager\MetadataPool::class
        );
        $this->batchRowsCount = $batchRowsCount;
        $this->activeTableSwitcher = $activeTableSwitcher ?: $objectManager->get(ActiveTableSwitcher::class);
    }

    /**
     * Refresh entities index
     *
     * @return $this
     */
    public function execute()
    {
        $this->reindex();
        $this->activeTableSwitcher->switchTable($this->connection, [$this->getMainTable()]);
        return $this;
    }

    /**
     * Return select for remove unnecessary data
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function getSelectUnnecessaryData()
    {
        return $this->connection->select()->from(
            $this->getMainTable(),
            []
        )->joinLeft(
            ['t' => $this->getMainTable()],
            $this->getMainTable() .
            '.category_id = t.category_id AND ' .
            $this->getMainTable() .
            '.store_id = t.store_id AND ' .
            $this->getMainTable() .
            '.product_id = t.product_id',
            []
        )->where(
            't.category_id IS NULL'
        );
    }

    /**
     * Remove unnecessary data
     *
     * @return void
     */
    protected function removeUnnecessaryData()
    {
        $this->connection->query(
            $this->connection->deleteFromSelect($this->getSelectUnnecessaryData(), $this->getMainTable())
        );
    }

    /**
     * Publish data from tmp to index
     *
     * @return void
     */
    protected function publishData()
    {
        $select = $this->connection->select()->from($this->getMainTmpTable());
        $columns = array_keys($this->connection->describeTable($this->getMainTable()));
        $tableName = $this->activeTableSwitcher->getAdditionalTableName($this->getMainTable());

        $this->connection->query(
            $this->connection->insertFromSelect(
                $select,
                $tableName,
                $columns,
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            )
        );
    }

    /**
     * Clear all index data
     *
     * @return void
     */
    protected function clearTmpData()
    {
        $this->connection->delete($this->getMainTmpTable());
    }

    /**
     * {@inheritdoc}
     */
    protected function reindexRootCategory(\Magento\Store\Model\Store $store)
    {
        if ($this->isIndexRootCategoryNeeded()) {
            $this->reindexCategoriesBySelect($this->getAllProducts($store), 'cp.entity_id IN (?)');
        }
    }

    /**
     * Reindex products of anchor categories
     *
     * @param \Magento\Store\Model\Store $store
     * @return void
     */
    protected function reindexAnchorCategories(\Magento\Store\Model\Store $store)
    {
        $this->reindexCategoriesBySelect($this->getAnchorCategoriesSelect($store), 'ccp.product_id IN (?)');
    }

    /**
     * Reindex products of non anchor categories
     *
     * @param \Magento\Store\Model\Store $store
     * @return void
     */
    protected function reindexNonAnchorCategories(\Magento\Store\Model\Store $store)
    {
        $this->reindexCategoriesBySelect($this->getNonAnchorCategoriesSelect($store), 'ccp.product_id IN (?)');
    }

    /**
     * Reindex categories using given SQL select and condition.
     *
     * @param \Magento\Framework\DB\Select $basicSelect
     * @param string $whereCondition
     * @return void
     */
    private function reindexCategoriesBySelect(\Magento\Framework\DB\Select $basicSelect, $whereCondition)
    {
        $entityMetadata = $this->metadataPool->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
        $columns = array_keys($this->connection->describeTable($this->getMainTmpTable()));
        $this->batchSizeManagement->ensureBatchSize($this->connection, $this->batchRowsCount);
        $batches = $this->batchProvider->getBatches(
            $this->connection,
            $entityMetadata->getEntityTable(),
            $entityMetadata->getIdentifierField(),
            $this->batchRowsCount
        );
        foreach ($batches as $batch) {
            $this->clearTmpData();
            $resultSelect = clone $basicSelect;
            $select = $this->connection->select();
            $select->distinct(true);
            $select->from(['e' => $entityMetadata->getEntityTable()], $entityMetadata->getIdentifierField());
            $entityIds = $this->batchProvider->getBatchIds($this->connection, $select, $batch);
            $resultSelect->where($whereCondition, $entityIds);
            $this->connection->query(
                $this->connection->insertFromSelect(
                    $resultSelect,
                    $this->getMainTmpTable(),
                    $columns,
                    \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
                )
            );
            $this->publishData();
            $this->removeUnnecessaryData();
        }
    }
}
