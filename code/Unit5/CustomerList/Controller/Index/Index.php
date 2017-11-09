<?php

namespace Unit5\CustomerList\Controller\Index;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;

/**
 * Class Index
 * @package Unit5\CustomerList\Controller\Index\Index
 */
class Index extends Action
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepo;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * Index constructor.
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepo
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     */
    public function __construct(Context $context,
                                CustomerRepositoryInterface $customerRepo,
                                SearchCriteriaBuilder $searchCriteriaBuilder,
                                FilterBuilder $filterBuilder,
                                FilterGroupBuilder $filterGroupBuilder)
    {
        $this->customerRepo = $customerRepo;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;

        return parent::__construct($context);
    }

    public function execute()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/plain');

        $customers = $this->getCustomersFromRepository();
        $this->getResponse()->appendBody(
            sprintf("List contains %s\n\n", get_class($customers[0]))
        );

        foreach ($customers as $customer) {
            $this->output($customer);
        }
    }

    private function setFilters()
    {
        $this->addEmailFilter();
        $this->addNameFilter();
        $this->searchCriteriaBuilder->setFilterGroups([$this->filterGroupBuilder->create()]);

    }

    private function addEmailFilter()
    {
        $emailFilter = $this->filterBuilder
            ->setField('email')
            ->setValue('%@gmail.com')
            ->setConditionType('like')
            ->create();
        $this->filterGroupBuilder->addFilter($emailFilter);
    }

    private function addNameFilter()
    {
        $nameFilter = $this->filterBuilder
            ->setField('firstname')
            ->setValue('Johana')
            ->setConditionType('eq')
            ->create();
        $this->filterGroupBuilder->addFilter($nameFilter);
    }

    /**
     * @return CustomerInterface[]
     */
    private function getCustomersFromRepository()
    {
        $this->setFilters();
        $criteria = $this->searchCriteriaBuilder->create();
        $customers = $this->customerRepo->getList($criteria);
        return $customers->getItems();
    }

    /**
     * @param CustomerInterface $customer
     */
    private function output(CustomerInterface $customer)
    {
        $this->getResponse()->appendBody(sprintf(
            "\"%s %s\" <%s> (%s)\n",
            $customer->getFirstname(),
            $customer->getLastname(),
            $customer->getEmail(),
            $customer->getId()
        ));
    }
}
