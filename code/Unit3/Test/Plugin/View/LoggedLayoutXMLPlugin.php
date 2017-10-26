<?php

namespace Unit3\Test\Plugin\View;

use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;

class LoggedLayoutXMLPlugin
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * LogPageOutput constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }
    public function afterGenerateXml(LayoutInterface $subject, $result)
    {
        $this->_logger->debug($subject->getUpdate()->asSimplexml()->count());

        return $result;
    }
}
