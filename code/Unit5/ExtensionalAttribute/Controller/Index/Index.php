<?php

namespace Unit5\ExtensionalAttribute\Controller\Index;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;

/**
 * @package Unit5\ExtensionalAttribute\Controller\Index\Index
 */
class Index extends Action
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $repo;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param CategoryRepositoryInterface $repo
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $repo,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->repo = $repo;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);
    }

    /**
     * Function execute
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $category = $this->repo->get(1);

        $this->getResponse()->appendBody(sprintf(
                "%s (%d) country - %s\n",
                $category->getName(),
                $category->getId(),
                $category->getExtensionAttributes()->getCountries()->getCountryCode()
            )
        );
    }
}
