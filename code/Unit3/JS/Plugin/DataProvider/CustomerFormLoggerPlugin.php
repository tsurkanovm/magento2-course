<?php

namespace Unit3\JS\Plugin\DataProvider;

use Magento\Customer\Model\Customer\DataProvider;
use Psr\Log\LoggerInterface;

class CustomerFormLoggerPlugin
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function afterGetData(DataProvider $provider, $result)
    {
        $message = print_r($result, true);
        $this->logger->info($message);

        return $result;
    }
}
