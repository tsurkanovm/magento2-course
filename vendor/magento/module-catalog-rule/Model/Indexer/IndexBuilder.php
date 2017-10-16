<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRule\Model\Indexer;

use Magento\Catalog\Model\Product;
use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\CatalogRule\Model\Rule;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @since 100.0.2
 */
class IndexBuilder
{
    const SECONDS_IN_DAY = 86400;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     * @deprecated 100.2.0
     * @since 100.1.0
     */
    protected $metadataPool;

    /**
     * CatalogRuleGroupWebsite columns list
     *
     * This array contain list of CatalogRuleGroupWebsite table columns
     *
     * @var array
     * @deprecated 100.2.0
     */
    protected $_catalogRuleGroupWebsiteColumnsList = ['rule_id', 'customer_group_id', 'website_id'];

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RuleCollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateFormat;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var Product[]
     */
    protected $loadedProducts;

    /**
     * @var int
     */
    protected $batchCount;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\CatalogRule\Model\Indexer\ProductPriceCalculator
     */
    private $productPriceCalculator;

    /**
     * @var \Magento\CatalogRule\Model\Indexer\ReindexRuleProduct
     */
    private $reindexRuleProduct;

    /**
     * @var \Magento\CatalogRule\Model\Indexer\ReindexRuleGroupWebsite
     */
    private $reindexRuleGroupWebsite;

    /**
     * @var \Magento\CatalogRule\Model\Indexer\RuleProductsSelectBuilder
     */
    private $ruleProductsSelectBuilder;

    /**
     * @var \Magento\CatalogRule\Model\Indexer\ReindexRuleProductPrice
     */
    private $reindexRuleProductPrice;

    /**
     * @var \Magento\CatalogRule\Model\Indexer\RuleProductPricesPersistor
     */
    private $pricesPersistor;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Indexer\ActiveTableSwitcher
     */
    private $activeTableSwitcher;

    /**
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Stdlib\DateTime $dateFormat
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param int $batchCount
     * @param \Magento\CatalogRule\Model\Indexer\ProductPriceCalculator|null $productPriceCalculator
     * @param \Magento\CatalogRule\Model\Indexer\ReindexRuleProduct|null $reindexRuleProduct
     * @param \Magento\CatalogRule\Model\Indexer\ReindexRuleGroupWebsite|null $reindexRuleGroupWebsite
     * @param \Magento\CatalogRule\Model\Indexer\RuleProductsSelectBuilder|null $ruleProductsSelectBuilder
     * @param \Magento\CatalogRule\Model\Indexer\ReindexRuleProductPrice|null $reindexRuleProductPrice
     * @param \Magento\CatalogRule\Model\Indexer\RuleProductPricesPersistor|null $pricesPersistor
     * @param \Magento\Catalog\Model\ResourceModel\Indexer\ActiveTableSwitcher|null $activeTableSwitcher
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        RuleCollectionFactory $ruleCollectionFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Stdlib\DateTime $dateFormat,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        $batchCount = 1000,
        \Magento\CatalogRule\Model\Indexer\ProductPriceCalculator $productPriceCalculator = null,
        \Magento\CatalogRule\Model\Indexer\ReindexRuleProduct $reindexRuleProduct = null,
        \Magento\CatalogRule\Model\Indexer\ReindexRuleGroupWebsite $reindexRuleGroupWebsite = null,
        \Magento\CatalogRule\Model\Indexer\RuleProductsSelectBuilder $ruleProductsSelectBuilder = null,
        \Magento\CatalogRule\Model\Indexer\ReindexRuleProductPrice $reindexRuleProductPrice = null,
        \Magento\CatalogRule\Model\Indexer\RuleProductPricesPersistor $pricesPersistor = null,
        \Magento\Catalog\Model\ResourceModel\Indexer\ActiveTableSwitcher $activeTableSwitcher = null
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->storeManager = $storeManager;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->logger = $logger;
        $this->priceCurrency = $priceCurrency;
        $this->eavConfig = $eavConfig;
        $this->dateFormat = $dateFormat;
        $this->dateTime = $dateTime;
        $this->productFactory = $productFactory;
        $this->batchCount = $batchCount;

        $this->productPriceCalculator = $productPriceCalculator ?: ObjectManager::getInstance()->get(
            \Magento\CatalogRule\Model\Indexer\ProductPriceCalculator::class
        );
        $this->reindexRuleProduct = $reindexRuleProduct ?: ObjectManager::getInstance()->get(
            \Magento\CatalogRule\Model\Indexer\ReindexRuleProduct::class
        );
        $this->reindexRuleGroupWebsite = $reindexRuleGroupWebsite ?: ObjectManager::getInstance()->get(
            \Magento\CatalogRule\Model\Indexer\ReindexRuleGroupWebsite::class
        );
        $this->ruleProductsSelectBuilder = $ruleProductsSelectBuilder ?: ObjectManager::getInstance()->get(
            \Magento\CatalogRule\Model\Indexer\RuleProductsSelectBuilder::class
        );
        $this->reindexRuleProductPrice = $reindexRuleProductPrice ?: ObjectManager::getInstance()->get(
            \Magento\CatalogRule\Model\Indexer\ReindexRuleProductPrice::class
        );
        $this->pricesPersistor = $pricesPersistor ?: ObjectManager::getInstance()->get(
            \Magento\CatalogRule\Model\Indexer\RuleProductPricesPersistor::class
        );
        $this->activeTableSwitcher = $activeTableSwitcher ?: ObjectManager::getInstance()->get(
            \Magento\Catalog\Model\ResourceModel\Indexer\ActiveTableSwitcher::class
        );
    }

    /**
     * Reindex by id
     *
     * @param int $id
     * @return void
     * @api
     */
    public function reindexById($id)
    {
        $this->reindexByIds([$id]);
    }

    /**
     * Reindex by ids
     *
     * @param array $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     * @api
     */
    public function reindexByIds(array $ids)
    {
        try {
            $this->doReindexByIds($ids);
        } catch (\Exception $e) {
            $this->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Catalog rule indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * Reindex by ids. Template method
     *
     * @param array $ids
     * @return void
     */
    protected function doReindexByIds($ids)
    {
        $this->cleanByIds($ids);

        foreach ($this->getActiveRules() as $rule) {
            foreach ($ids as $productId) {
                $this->applyRule($rule, $this->getProduct($productId));
            }
        }
    }

    /**
     * Full reindex
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     * @api
     */
    public function reindexFull()
    {
        try {
            $this->doReindexFull();
        } catch (\Exception $e) {
            $this->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Catalog rule indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * Full reindex Template method
     *
     * @return void
     */
    protected function doReindexFull()
    {
        $this->connection->truncateTable(
            $this->getTable($this->activeTableSwitcher->getAdditionalTableName('catalogrule_product'))
        );
        $this->connection->truncateTable(
            $this->getTable($this->activeTableSwitcher->getAdditionalTableName('catalogrule_product_price'))
        );

        foreach ($this->getAllRules() as $rule) {
            $this->reindexRuleProduct->execute($rule, $this->batchCount, true);
        }

        $this->reindexRuleProductPrice->execute($this->batchCount, null, true);
        $this->reindexRuleGroupWebsite->execute(true);

        $this->activeTableSwitcher->switchTable(
            $this->connection,
            [
                $this->getTable('catalogrule_product'),
                $this->getTable('catalogrule_product_price'),
                $this->getTable('catalogrule_group_website')
            ]
        );
    }

    /**
     * Clean by product ids
     *
     * @param array $productIds
     * @return void
     */
    protected function cleanByIds($productIds)
    {
        $query = $this->connection->deleteFromSelect(
            $this->connection
                ->select()
                ->from($this->resource->getTableName('catalogrule_product'), 'product_id')
                ->distinct()
                ->where('product_id IN (?)', $productIds),
            $this->resource->getTableName('catalogrule_product')
        );
        $this->connection->query($query);

        $query = $this->connection->deleteFromSelect(
            $this->connection->select()
                ->from($this->resource->getTableName('catalogrule_product_price'), 'product_id')
                ->distinct()
                ->where('product_id IN (?)', $productIds),
            $this->resource->getTableName('catalogrule_product_price')
        );
        $this->connection->query($query);
    }

    /**
     * @param Rule $rule
     * @param Product $product
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function applyRule(Rule $rule, $product)
    {
        $ruleId = $rule->getId();
        $productEntityId = $product->getId();
        $websiteIds = array_intersect($product->getWebsiteIds(), $rule->getWebsiteIds());

        if (!$rule->validate($product)) {
            return $this;
        }

        $this->connection->delete(
            $this->resource->getTableName('catalogrule_product'),
            [
                $this->connection->quoteInto('rule_id = ?', $ruleId),
                $this->connection->quoteInto('product_id = ?', $productEntityId)
            ]
        );

        $customerGroupIds = $rule->getCustomerGroupIds();
        $fromTime = strtotime($rule->getFromDate());
        $toTime = strtotime($rule->getToDate());
        $toTime = $toTime ? $toTime + self::SECONDS_IN_DAY - 1 : 0;
        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = $rule->getDiscountAmount();
        $actionStop = $rule->getStopRulesProcessing();

        $rows = [];
        try {
            foreach ($websiteIds as $websiteId) {
                foreach ($customerGroupIds as $customerGroupId) {
                    $rows[] = [
                        'rule_id' => $ruleId,
                        'from_time' => $fromTime,
                        'to_time' => $toTime,
                        'website_id' => $websiteId,
                        'customer_group_id' => $customerGroupId,
                        'product_id' => $productEntityId,
                        'action_operator' => $actionOperator,
                        'action_amount' => $actionAmount,
                        'action_stop' => $actionStop,
                        'sort_order' => $sortOrder,
                    ];

                    if (count($rows) == $this->batchCount) {
                        $this->connection->insertMultiple($this->getTable('catalogrule_product'), $rows);
                        $rows = [];
                    }
                }
            }

            if (!empty($rows)) {
                $this->connection->insertMultiple($this->resource->getTableName('catalogrule_product'), $rows);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        $this->reindexRuleProductPrice->execute($this->batchCount, $product);
        $this->reindexRuleGroupWebsite->execute();

        return $this;
    }

    /**
     * @param string $tableName
     * @return string
     */
    protected function getTable($tableName)
    {
        return $this->resource->getTableName($tableName);
    }

    /**
     * @param Rule $rule
     * @return $this
     * @deprecated 100.2.0
     * @see \Magento\CatalogRule\Model\Indexer\ReindexRuleProduct::execute
     */
    protected function updateRuleProductData(Rule $rule)
    {
        $ruleId = $rule->getId();
        if ($rule->getProductsFilter()) {
            $this->connection->delete(
                $this->getTable('catalogrule_product'),
                ['rule_id=?' => $ruleId, 'product_id IN (?)' => $rule->getProductsFilter()]
            );
        } else {
            $this->connection->delete(
                $this->getTable('catalogrule_product'),
                $this->connection->quoteInto('rule_id=?', $ruleId)
            );
        }

        $this->reindexRuleProduct->execute($rule, $this->batchCount);
        return $this;
    }

    /**
     * @param Product|null $product
     * @throws \Exception
     * @return $this
     * @deprecated 100.2.0
     * @see \Magento\CatalogRule\Model\Indexer\ReindexRuleProductPrice::execute
     * @see \Magento\CatalogRule\Model\Indexer\ReindexRuleGroupWebsite::execute
     */
    protected function applyAllRules(Product $product = null)
    {
        $this->reindexRuleProductPrice->execute($this->batchCount, $product);
        $this->reindexRuleGroupWebsite->execute();
        return $this;
    }

    /**
     * Update CatalogRuleGroupWebsite data
     *
     * @return $this
     * @deprecated 100.2.0
     * @see \Magento\CatalogRule\Model\Indexer\ReindexRuleGroupWebsite::execute
     */
    protected function updateCatalogRuleGroupWebsiteData()
    {
        $this->reindexRuleGroupWebsite->execute();
        return $this;
    }

    /**
     * Clean rule price index
     *
     * @return $this
     */
    protected function deleteOldData()
    {
        $this->connection->delete($this->getTable('catalogrule_product_price'));
        return $this;
    }

    /**
     * @param array $ruleData
     * @param null $productData
     * @return float
     * @deprecated 100.2.0
     * @see \Magento\CatalogRule\Model\Indexer\ProductPriceCalculator::calculate
     */
    protected function calcRuleProductPrice($ruleData, $productData = null)
    {
        return $this->productPriceCalculator->calculate($ruleData, $productData);
    }

    /**
     * @param int $websiteId
     * @param Product|null $product
     * @return \Zend_Db_Statement_Interface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @deprecated 100.2.0
     * @see \Magento\CatalogRule\Model\Indexer\RuleProductsSelectBuilder::build
     */
    protected function getRuleProductsStmt($websiteId, Product $product = null)
    {
        return $this->ruleProductsSelectBuilder->build($websiteId, $product);
    }

    /**
     * @param array $arrData
     * @return $this
     * @throws \Exception
     * @deprecated 100.2.0
     * @see \Magento\CatalogRule\Model\Indexer\RuleProductPricesPersistor::execute
     */
    protected function saveRuleProductPrices($arrData)
    {
        $this->pricesPersistor->execute($arrData);
        return $this;
    }

    /**
     * Get active rules
     *
     * @return array
     */
    protected function getActiveRules()
    {
        return $this->ruleCollectionFactory->create()->addFieldToFilter('is_active', 1);
    }

    /**
     * Get active rules
     *
     * @return array
     */
    protected function getAllRules()
    {
        return $this->ruleCollectionFactory->create();
    }

    /**
     * @param int $productId
     * @return Product
     */
    protected function getProduct($productId)
    {
        if (!isset($this->loadedProducts[$productId])) {
            $this->loadedProducts[$productId] = $this->productFactory->create()->load($productId);
        }
        return $this->loadedProducts[$productId];
    }

    /**
     * @param \Exception $e
     * @return void
     */
    protected function critical($e)
    {
        $this->logger->critical($e);
    }
}
