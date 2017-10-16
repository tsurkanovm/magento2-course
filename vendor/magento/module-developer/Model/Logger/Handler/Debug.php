<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Developer\Model\Logger\Handler;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\DeploymentConfig;

/**
 * Enable/disable debug logging based on the store config setting
 */
class Debug extends \Magento\Framework\Logger\Handler\Debug
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @param DriverInterface $filesystem
     * @param State $state
     * @param ScopeConfigInterface $scopeConfig
     * @param DeploymentConfig $deploymentConfig
     * @param string $filePath
     */
    public function __construct(
        DriverInterface $filesystem,
        State $state,
        ScopeConfigInterface $scopeConfig,
        DeploymentConfig $deploymentConfig,
        $filePath = null
    ) {
        parent::__construct($filesystem, $filePath);

        $this->state = $state;
        $this->scopeConfig = $scopeConfig;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        if ($this->deploymentConfig->isAvailable()) {
            return
                parent::isHandling($record)
                && $this->scopeConfig->getValue('dev/debug/debug_logging', ScopeInterface::SCOPE_STORE);
        }

        return parent::isHandling($record);
    }
}
