<?php

namespace Unit6\News\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Unit6\News\Model\News;
use Unit6\News\Model\ResourceModel\News\Collection;
use Unit6\News\Model\ResourceModel\News\CollectionFactory as NewsCollectionFactory;
use Magento\Framework\Message\Error;
use Magento\Backend\Model\View\Result\Page;

/**
 * Class Index
 * @package Unit6\News\Controller\Adminhtml\Index\Index
 */
class Edit extends Action
{
    const ADMIN_RESOURCE = 'Unit6_News::grid';

    /**
     * @var NewsCollectionFactory
     */
    protected $newsCollectionFactory;

    /**
     * @var NewsCollectionFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param NewsCollectionFactory $newsCollectionFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory, NewsCollectionFactory $newsCollectionFactory)
    {
        $this->newsCollectionFactory = $newsCollectionFactory;
        $this->resultPageFactory = $resultPageFactory;

        return parent::__construct($context);
    }

    /**
     * @return $this|Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('news_id');
        /* @var Collection $newsCollection*/
        $newsCollection = $this->newsCollectionFactory->create();

        /* @var News $newsModel*/
        if ($id && !($newsModel = $newsCollection->getItemById($id))) {
                $this->messageManager->addMessage(new Error('This post no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
        }

        /** @var  Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit News') : __('New News'),
            $id ? __('Edit News') : __('New News')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('News'));
        $resultPage->getConfig()->getTitle()->prepend($newsModel->getId() ? $newsModel->getTitle() : __('New News'));
        return $resultPage;
    }


    /**
     * @param Page $resultPage
     * @return Page
     */
    protected function initPage(Page $resultPage)
    {
        $resultPage->setActiveMenu('Unit6_News::news')
            ->addBreadcrumb(__('News'), __('News'))
            ->addBreadcrumb(__('News'), __('News'));

        return $resultPage;
    }
}
