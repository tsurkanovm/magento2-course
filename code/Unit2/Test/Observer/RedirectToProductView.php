<?php

namespace Unit2\Test\Observer;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class RedirectToProductView implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var UrlInterface
     */
    protected $_urlInterFace;

    /**
     * @var ActionFlag
     */
    protected $_actionFlag;

    /**
     * @param ActionFlag $_actionFlag
     * @param UrlInterface $_urlInterFace
     * @param LoggerInterface $_logger
     */
    public function __construct(
        ActionFlag $_actionFlag,
        UrlInterface $_urlInterFace,
        LoggerInterface $_logger
    )
    {
        $this->_actionFlag = $_actionFlag;
        $this->_urlInterFace = $_urlInterFace;
        $this->_logger = $_logger;
    }

    public function execute(Observer $observer)
    {
//        $request = $observer->getEvent()->getData('request');
//
//        if (($request->getModuleName() == 'catalog' || $request->getModuleName() == 'review')
//            & $request->getControllerName() == 'product') {
//            return;
//        }
//
//        $url = $this->_urlInterFace->getUrl('catalog/product/view/id/1');
//        $this->_logger->debug('Url->' . $url);
//        $action = $observer->getEvent()->getControllerAction();
//        $this->_actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
//        $action->getResponse()->setRedirect($url);
    }
}
