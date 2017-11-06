<?php

namespace Unit4\Test\Controller\Store;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Store\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\ResourceModel\Store\Collection as StoreCollection;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Store\Model\Store;

/**
 * Class Index
 * @package Unit4\Test\Controller\Store\Index
 */
class Index extends Action
{
    /* @var StoreCollection */
    private $storeCollection;

    /* @var CategoryCollectionFactory */
    private $categoryCollectionFactory;

    public function __construct(Context $context,
                                StoreCollectionFactory $storeCollectionFactory,
                                CategoryCollectionFactory $categoryCollectionFactory)
    {
        $this->storeCollection    = $storeCollectionFactory->create();
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        return parent::__construct($context);
    }


    public function execute()
    {
        $cats = [];
        $stores = [];
        /* @var $store Store*/
        foreach ($this->storeCollection as $store){
            $categoryCollection = $this->categoryCollectionFactory->create();
            $categoryCollection->addAttributeToSelect('name');
            $categoryCollection->addFilter('entity_id', $store->getRootCategoryId());

            foreach ($categoryCollection as $category){
                $cats[] = array(
                    'name' => $category->getName(),
                    'id'   => $category->getId()
                );
            }

            $stores[] = array(
                'store_id' => $store->getId(),
                'name'     => $store->getName(),
                'root_category_id' => $store->getRootCategoryId(),
                'category' => $cats
            );
        }

        $str = json_encode($stores);
        $this->getResponse()->appendBody($str);
    }
}
