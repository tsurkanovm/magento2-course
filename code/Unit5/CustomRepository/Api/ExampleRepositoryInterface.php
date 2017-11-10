<?php

namespace Unit5\CustomRepository\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface ExampleRepositoryInterface
{
    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
