<?php

namespace Training\Test\Controller\Adminhtml\Action;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Class Index
 * @package Training\Test\Controller\Adminhtml\Action\Index
 */
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
        $this->getResponse()->appendBody("HELLO WORLD");
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @return bool
     */
//    protected function _isAllowed() {
//        $secret = $this->getRequest()->getParam('secret');
//        return isset($secret) && (int)$secret==1;
//    }
}
