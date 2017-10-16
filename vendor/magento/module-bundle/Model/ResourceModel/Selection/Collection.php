<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Bundle\Model\ResourceModel\Selection;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitationFactory;
use Magento\Framework\App\ObjectManager;

/**
 * Bundle Selections Resource Collection
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Selection table name
     *
     * @var string
     */
    protected $_selectionTable;

    /**
     * @var DataObject
     */
    private $itemPrototype = null;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Product\CollectionProcessor
     */
    private $catalogRuleProcessor = null;

    /**
     * Is website scope prices joined to collection
     *
     * @var bool
     */
    private $websiteScopePriceJoined = false;

    /**
     * Initialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setRowIdFieldName('selection_id');
        $this->_selectionTable = $this->getTable('catalog_product_bundle_selection');
    }

    /**
     * Set store id for each collection item when collection was loaded
     *
     * @return $this
     */
    public function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getStoreId() && $this->_items) {
            foreach ($this->_items as $item) {
                $item->setStoreId($this->getStoreId());
            }
        }
        return $this;
    }

    /**
     * Initialize collection select
     *
     * @return $this|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(
            ['selection' => $this->_selectionTable],
            'selection.product_id = e.entity_id',
            ['*']
        );
    }

    /**
     * Join website scope prices to collection, override default prices
     *
     * @param int $websiteId
     * @return $this
     */
    public function joinPrices($websiteId)
    {
        $connection = $this->getConnection();
        $priceType = $connection->getCheckSql(
            'price.selection_price_type IS NOT NULL',
            'price.selection_price_type',
            'selection.selection_price_type'
        );
        $priceValue = $connection->getCheckSql(
            'price.selection_price_value IS NOT NULL',
            'price.selection_price_value',
            'selection.selection_price_value'
        );
        $this->getSelect()->joinLeft(
            ['price' => $this->getTable('catalog_product_bundle_selection_price')],
            'selection.selection_id = price.selection_id AND price.website_id = ' . (int)$websiteId .
            ' AND selection.parent_product_id = price.parent_product_id',
            [
                'selection_price_type' => $priceType,
                'selection_price_value' => $priceValue,
                'parent_product_id' => 'price.parent_product_id',
                'price_scope' => 'price.website_id'
            ]
        );
        $this->websiteScopePriceJoined = true;

        return $this;
    }

    /**
     * Apply option ids filter to collection
     *
     * @param array $optionIds
     * @return $this
     */
    public function setOptionIdsFilter($optionIds)
    {
        if (!empty($optionIds)) {
            $this->getSelect()->where('selection.option_id IN (?)', $optionIds);
        }
        return $this;
    }

    /**
     * Apply selection ids filter to collection
     *
     * @param array $selectionIds
     * @return $this
     */
    public function setSelectionIdsFilter($selectionIds)
    {
        if (!empty($selectionIds)) {
            $this->getSelect()->where('selection.selection_id IN (?)', $selectionIds);
        }
        return $this;
    }

    /**
     * Set position order
     *
     * @return $this
     */
    public function setPositionOrder()
    {
        $this->getSelect()->order('selection.position asc')->order('selection.selection_id asc');
        return $this;
    }

    /**
     * Add filtering of product then havent enoght stock
     *
     * @return $this
     * @since 100.2.0
     */
    public function addQuantityFilter()
    {
        $this->getSelect()
            ->joinInner(
                ['stock' => $this->getTable('cataloginventory_stock_status')],
                'selection.product_id = stock.product_id',
                []
            )
            ->where(
                '(selection.selection_can_change_qty or selection.selection_qty <= stock.qty) and stock.stock_status'
            );
        return $this;
    }

    /**
     * @inheritDoc
     * @since 100.2.0
     */
    public function getNewEmptyItem()
    {
        if (null === $this->itemPrototype) {
            $this->itemPrototype = parent::getNewEmptyItem();
        }
        return clone $this->itemPrototype;
    }

    /**
     * Add filter by price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $searchMin
     * @param bool $useRegularPrice
     *
     * @return $this
     * @since 100.2.0
     */
    public function addPriceFilter($product, $searchMin, $useRegularPrice = false)
    {
        if ($product->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC) {
            $this->addPriceData();
            if ($useRegularPrice) {
                $minimalPriceExpression = self::INDEX_TABLE_ALIAS . '.price';
            } else {
                $this->getCatalogRuleProcessor()->addPriceData($this, 'selection.product_id');
                $minimalPriceExpression = 'LEAST(minimal_price, IFNULL(catalog_rule_price, minimal_price))';
            }
            $orderByValue = new \Zend_Db_Expr(
                '(' .
                $minimalPriceExpression .
                ' * selection.selection_qty' .
                ')'
            );
        } else {
            $connection = $this->getConnection();
            $priceType = $connection->getIfNullSql(
                'price.selection_price_type',
                'selection.selection_price_type'
            );
            $priceValue = $connection->getIfNullSql(
                'price.selection_price_value',
                'selection.selection_price_value'
            );
            if (!$this->websiteScopePriceJoined) {
                $websiteId = $this->_storeManager->getStore()->getWebsiteId();
                $this->getSelect()->joinLeft(
                    ['price' => $this->getTable('catalog_product_bundle_selection_price')],
                    'selection.selection_id = price.selection_id AND price.website_id = ' . (int)$websiteId,
                    []
                );
            }
            $price = $connection->getCheckSql(
                $priceType . ' = 1',
                (float) $product->getPrice() . ' * '. $priceValue . ' / 100',
                $priceValue
            );
            $orderByValue = new \Zend_Db_Expr('('. $price. ' * '. 'selection.selection_qty)');
        }

        $this->getSelect()->reset(Select::ORDER);
        $this->getSelect()->order(new \Zend_Db_Expr($orderByValue . ($searchMin ? Select::SQL_ASC : Select::SQL_DESC)));
        $this->getSelect()->limit(1);
        return $this;
    }

    /**
     * @return \Magento\CatalogRule\Model\ResourceModel\Product\CollectionProcessor
     * @deprecated 100.2.0
     */
    private function getCatalogRuleProcessor()
    {
        if (null === $this->catalogRuleProcessor) {
            $this->catalogRuleProcessor = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\CatalogRule\Model\ResourceModel\Product\CollectionProcessor::class);
        }

        return $this->catalogRuleProcessor;
    }
}
