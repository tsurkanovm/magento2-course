<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\DeploymentConfig;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DriverPool;

/**
 * Deployment configuration reader.
 * Loads the merged configuration from config files.
 *
 * @see FileReader The reader for specific configuration file
 */
class Reader
{
    /**
     * @var DirectoryList
     */
    private $dirList;

    /**
     * @var ConfigFilePool
     */
    private $configFilePool;

    /**
     * @var DriverPool
     */
    private $driverPool;

    /**
     * Configuration file names
     *
     * @var array
     */
    private $files;

    /**
     * Constructor
     *
     * @param DirectoryList $dirList
     * @param DriverPool $driverPool
     * @param ConfigFilePool $configFilePool
     * @param null|string $file
     * @throws \InvalidArgumentException
     */
    public function __construct(
        DirectoryList $dirList,
        DriverPool $driverPool,
        ConfigFilePool $configFilePool,
        $file = null
    ) {
        $this->dirList = $dirList;
        $this->configFilePool = $configFilePool;
        $this->driverPool = $driverPool;
        if (null !== $file) {
            if (!preg_match('/^[a-z\d\.\-]+\.php$/i', $file)) {
                throw new \InvalidArgumentException("Invalid file name: {$file}");
            }
            $this->files = [$file];
        } else {
            $this->files = $this->configFilePool->getPaths();
        }
    }

    /**
     * Gets the file name
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Method loads merged configuration within all configuration files.
     * To retrieve specific file configuration, use FileReader.
     * $fileKey option is deprecated since version 2.2.0.
     *
     * @param string $fileKey The file key (deprecated)
     * @return array
     * @throws FileSystemException If file can not be read
     * @throws \Exception If file key is not correct
     * @see FileReader
     */
    public function load($fileKey = null)
    {
        $path = $this->dirList->getPath(DirectoryList::CONFIG);
        $fileDriver = $this->driverPool->getDriver(DriverPool::FILE);
        $result = [];
        if ($fileKey) {
            $filePath = $path . '/' . $this->configFilePool->getPath($fileKey);
            if ($fileDriver->isExists($filePath)) {
                $result = include $filePath;
            }
        } else {
            $configFiles = $this->configFilePool->getPaths();
            $allFilesData = [];
            $result = [];
            foreach (array_keys($configFiles) as $fileKey) {
                $configFile = $path . '/' . $this->configFilePool->getPath($fileKey);
                if ($fileDriver->isExists($configFile)) {
                    $fileData = include $configFile;
                } else {
                    continue;
                }
                $allFilesData[$configFile] = $fileData;
                if (is_array($fileData) && count($fileData) > 0) {
                    $result = array_replace_recursive($result, $fileData);
                }
            }
        }
        return $result ?: [];
    }

    /**
     * Loads the configuration file.
     *
     * @param string $fileKey The file key
     * @param string $pathConfig The path config
     * @param bool $ignoreInitialConfigFiles Whether ignore custom pools
     * @return array
     * @deprecated 100.2.0 Magento does not support custom config file pools since 2.2.0 version
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadConfigFile($fileKey, $pathConfig, $ignoreInitialConfigFiles = false)
    {
        return $this->load($fileKey);
    }
}
