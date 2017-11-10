<?php

namespace Unit5\CustomRepositoryUsage\Controller\Index;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Unit5\CustomRepository\Api\Data\ExampleInterface;
use Unit5\CustomRepository\Api\ExampleRepositoryInterface;

/**
 * @package Unit5\CustomRepositoryUsage\Controller\Index\Index
 */
class Index extends Action
{
    /**
     * @var ExampleRepositoryInterface
     */
    private $exampleRepository;

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
     * @param ExampleRepositoryInterface $exampleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        Context $context,
        ExampleRepositoryInterface $exampleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    )
    {
        $this->exampleRepository = $exampleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->getResponse()->setHeader('content-type', 'text/plain');

        $filters = array_map(function ($name) {
            return $this->filterBuilder
                ->setConditionType('eq')
                ->setField('name')
                ->setValue($name)
                ->create();
        }, ['Foo', 'Bar', 'Qux']);

        $this->searchCriteriaBuilder->addFilters($filters);
        $examples = $this->exampleRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        /** @var ExampleInterface $example */
        foreach ($examples as $example) {
            $this->getResponse()->appendBody(sprintf(
                "%s (%d)\n",
                $example->getName(),
                $example->getId()
            ));
        }
    }
}
