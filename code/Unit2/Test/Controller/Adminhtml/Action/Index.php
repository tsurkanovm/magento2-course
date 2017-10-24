<?php

namespace Unit2\Test\Controller\Adminhtml\Action;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Class Index
 * @package Unit2\Test\Controller\Adminhtml\Action\Index
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
    protected function _isAllowed() {
        $secret = $this->getRequest()->getParam('secret');
        return !empty($secret);
    }
}
