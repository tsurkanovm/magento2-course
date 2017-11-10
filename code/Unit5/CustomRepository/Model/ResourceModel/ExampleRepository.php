<?php

namespace Unit5\CustomRepository\Model\ResourceModel;

use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Unit5\CustomRepository\Api\Data\ExampleInterface;
use Unit5\CustomRepository\Api\ExampleRepositoryInterface;
use Unit5\CustomRepository\Model\Data\ExampleFactory as ExampleDataFactory;
use Unit5\CustomRepository\Model\ExampleFactory;
use Unit5\CustomRepository\Model\Example as ExampleModel;
use Unit5\CustomRepository\Model\ResourceModel\Example\Collection as ExampleCollection;

class ExampleRepository implements ExampleRepositoryInterface
{
    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ExampleFactory
     */
    private $exampleFactory;

    /**
     * @var ExampleDataFactory
     */
    private $exampleDataFactory;

    /**
     * ExampleRepository constructor.
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param ExampleFactory $exampleFactory
     * @param ExampleDataFactory $exampleDataFactory
     */
    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        ExampleFactory $exampleFactory,
        ExampleDataFactory $exampleDataFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->exampleFactory = $exampleFactory;
        $this->exampleDataFactory = $exampleDataFactory;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var ExampleCollection $collection */
        $collection = $this->exampleFactory->create()->getCollection();

        /** @var SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $this->applySearchCriteriaToCollection($searchCriteria, $collection);
        $examples = $this->convertCollectionToDataItemsArray($collection);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($examples);

        return $searchResults;
    }

    /**
     * @param FilterGroup $filterGroup
     * @param ExampleCollection $collection
     */
    private function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        ExampleCollection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?
                $filter->getConditionType() :
                'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * @param ExampleCollection $collection
     * @return array
     */
    private function convertCollectionToDataItemsArray(ExampleCollection $collection)
    {
        return array_map(function (ExampleModel $example) {
            /** @var ExampleInterface $dataObject */
            $dataObject = $this->exampleDataFactory->create();
            $dataObject->setAllData($example->getData());

            return $dataObject;
        }, $collection->getItems());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param ExampleCollection $collection
     */
    private function applySearchCriteriaToCollection(
        SearchCriteriaInterface $searchCriteria,
        ExampleCollection $collection
    ) {
        $this->applySearchCriteriaFiltersToCollection(
            $searchCriteria,
            $collection
        );
        $this->applySearchCriteriaSortOrdersToCollection(
            $searchCriteria,
            $collection
        );
        $this->applySearchCriteriaPagingToCollection(
            $searchCriteria,
            $collection
        );
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param ExampleCollection $collection
     */
    private function applySearchCriteriaFiltersToCollection(
        SearchCriteriaInterface $searchCriteria,
        ExampleCollection $collection
    ) {
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param ExampleCollection $collection
     */
    private function applySearchCriteriaSortOrdersToCollection(
        SearchCriteriaInterface $searchCriteria,
        ExampleCollection $collection
    ) {
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    $sortOrder->getDirection()
                );
            }
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param ExampleCollection $collection
     */
    private function applySearchCriteriaPagingToCollection(
        SearchCriteriaInterface $searchCriteria,
        ExampleCollection $collection
    ) {
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
    }
}
