<?php

namespace Unit5\Material\Controller\Show;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;

/**
 * Class Index
 * @package Unit5\Material\Controller\List\Index
 */
class Index extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(Context $context, CollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
        return parent::__construct($context);
    }


    public function execute()
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToFilter('custom_material', array('finset' => 241));

        foreach ($productCollection as $product) {
            $this->getResponse()->appendBody(sprintf(
                    "%s - %s (%d)\n",
                    $product->getName(),
                    $product->getSku(),
                    $product->getId())
            );
        }
    }
}
