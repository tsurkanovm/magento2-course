<?php

namespace Unit2\Test\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class LogPageOutput implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * LogPageOutput constructor.
     * @param LoggerInterface $_logger
     */
    public function __construct(LoggerInterface $_logger)
    {
        $this->_logger = $_logger;
    }

    public function execute(Observer $observer)
    {
        $response = $observer->getEvent()->getData('response');
        $body = $response->getBody();
//        $this->_logger->debug("--------\n\n\n BODY \n\n\n ". $body);
    }
}
