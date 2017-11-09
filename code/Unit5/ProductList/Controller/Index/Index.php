<?php

namespace Unit5\ProductList\Controller\Index;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type\Virtual;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class Index
 * @package Unit5\ProductList\Controller\Index\Index
 */
class Index extends Action
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepo;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * Index constructor.
     * @param Context $context
     * @param ProductRepositoryInterface $productRepo
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(Context $context,
                                ProductRepositoryInterface $productRepo,
                                SearchCriteriaBuilder $searchCriteriaBuilder,
                                FilterBuilder $filterBuilder,
                                SortOrderBuilder $sortOrderBuilder)
    {
        $this->productRepo = $productRepo;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;

        return parent::__construct($context);
    }

    public function execute()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/plain');
        $products = $this->getProductsFromRepository();
        foreach ($products as $product) {
            $this->outputProduct($product);
        }
    }

    private function setProductTypeFilter()
    {
        // =========== OR =========
//        $configProductFilter1 = $this->filterBuilder
//            ->setField('type_id')
//            ->setValue('grouped')
//            ->setConditionType('eq')
//            ->create();
//        $configProductFilter2 = $this->filterBuilder
//            ->setField('name')
//            ->setValue('M%')
//            ->setConditionType('like')
//            ->create();
//
//        $this->searchCriteriaBuilder->addFilters([$configProductFilter1, $configProductFilter2]);
        // =====================================================================================

        // ========== AND ===================================================================
        $this->searchCriteriaBuilder->addFilter('type_id', Configurable::TYPE_CODE, 'eq');
        $this->searchCriteriaBuilder->addFilter('name', 'M%', 'like');
        // ==================================================================================
    }

    private function setProductOrder()
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField('entity_id')
            ->setAscendingDirection()
            ->create();

        $this->searchCriteriaBuilder->addSortOrder($sortOrder);
        $this->searchCriteriaBuilder->setPageSize(6);
        $this->searchCriteriaBuilder->setCurrentPage(2);
    }

    private function getProductsFromRepository()
    {
        $this->setProductTypeFilter();
        $this->setProductOrder();
        $criteria = $this->searchCriteriaBuilder->create();
        $products = $this->productRepo->getList($criteria);
        return $products->getItems();
    }

    private function outputProduct(ProductInterface $product)
    {
        $this->getResponse()->appendBody(sprintf(
                "%s - %s (%d)\n",
                $product->getName(),
                $product->getSku(),
                $product->getId())
        );
    }
}
