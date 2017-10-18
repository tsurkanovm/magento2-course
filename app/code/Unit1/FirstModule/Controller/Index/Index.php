<?php

namespace Unit1\FirstModule\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;

class Index extends Action
{
    /**
     * Index resultPageFactory
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        return parent::__construct($context);
    }

    /**
     * Function execute
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $myTestClass = $this->_objectManager->get('Unit1\FirstModule\MagentoU\Test');
        $testParam = [$myTestClass->justAParameter,$myTestClass->data];
        print_r($testParam);
        exit;

//        return $this->resultPageFactory->create();
    }
}
