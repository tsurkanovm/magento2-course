<?php

namespace Training\Test\App;

use \Magento\Framework\App\FrontController as FrontControllerBase;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\RouterList;
use Psr\Log\LoggerInterface;

class FrontController extends FrontControllerBase
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param RouterList $routerList
     * @param Http $response
     * @param LoggerInterface $logger
     */
    public function __construct(RouterList $routerList, Http $response, LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct($routerList, $response);
    }

    /**
     * @inheritdoc
     */
    public function dispatch(RequestInterface $request) {
        foreach ($this->_routerList as $router) {
            $this->logger->debug(get_class($router));
        }
        return parent::dispatch($request);
    }
}
