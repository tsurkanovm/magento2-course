<?php

namespace Training\Test\App\Router;

use Magento\Framework\App\RequestInterface;
use \Magento\Framework\App\Router\NoRouteHandlerInterface;

class NoRouteHandler implements NoRouteHandlerInterface
{
    public function process(RequestInterface $request) {
        $moduleName     = 'cms';
        $controllerName = 'index';
        $actionName     = 'index';

        $request
            ->setModuleName($moduleName)
            ->setControllerName($controllerName)
            ->setActionName($actionName);

        return true;
    }
}
