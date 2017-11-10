<?php

namespace Unit5\Material\Controller\Show;

use Magento\Catalog\Model\Product;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class Index
 * @package Unit5\Material\Controller\List\Index
 */
class Repo extends Action
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * Index constructor.
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    )
    {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        parent::__construct($context);
    }


    public function execute()
    {
        $filter = $this->filterBuilder
            ->setField('custom_material')
            ->setValue(241)
            ->setConditionType('finset')
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filter]);
        $products = $this->productRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($products->getItems() as $product) {
            $this->getResponse()->appendBody(sprintf(
                    "%s - %s (%d)\n",
                    $product->getName(),
                    $product->getSku(),
                    $product->getId())
            );
        }
    }
}
