<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Authorizenet\Model\Directpost\Request;

use Magento\Authorizenet\Model\Request\Factory as AuthorizenetRequestFactory;

/**
 * Factory class for @see \Magento\Authorizenet\Model\Directpost\Request
 */
class Factory extends AuthorizenetRequestFactory
{
    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = \Magento\Authorizenet\Model\Directpost\Request::class
    ) {
        parent::__construct($objectManager, $instanceName);
    }
}
