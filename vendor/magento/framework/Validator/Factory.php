<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Validator;

use Magento\Framework\Cache\FrontendInterface;

class Factory
{
    /** cache key */
    const CACHE_KEY = __CLASS__;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Validator config files
     *
     * @var array|null
     */
    protected $_configFiles = null;

    /**
     * @var bool
     */
    private $isDefaultTranslatorInitialized = false;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $moduleReader;

    /**
     * @var FrontendInterface
     */
    private $cache;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Framework\Config\FileIteratorFactory
     */
    private $fileIteratorFactory;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param FrontendInterface $cache
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        FrontendInterface $cache
    ) {
        $this->_objectManager = $objectManager;
        $this->moduleReader = $moduleReader;
        $this->cache = $cache;
    }

    /**
     * Init cached list of validation files
     *
     * @return void
     */
    protected function _initializeConfigList()
    {
        if (!$this->_configFiles) {
            $this->_configFiles = $this->cache->load(self::CACHE_KEY);
            if (!$this->_configFiles) {
                $this->_configFiles = $this->moduleReader->getConfigurationFiles('validation.xml');
                $this->cache->save(
                    $this->getSerializer()->serialize($this->_configFiles->toArray()),
                    self::CACHE_KEY
                );
            } else {
                $filesArray = $this->getSerializer()->unserialize($this->_configFiles);
                $this->_configFiles = $this->getFileIteratorFactory()->create(array_keys($filesArray));
            }
        }
    }

    /**
     * Create and set default translator to \Magento\Framework\Validator\AbstractValidator.
     *
     * @return void
     */
    protected function _initializeDefaultTranslator()
    {
        if (!$this->isDefaultTranslatorInitialized) {
            // Pass translations to \Magento\Framework\TranslateInterface from validators
            $translatorCallback = function () {
                $argc = func_get_args();
                return (string)new \Magento\Framework\Phrase(array_shift($argc), $argc);
            };
            /** @var \Magento\Framework\Translate\Adapter $translator */
            $translator = $this->_objectManager->create(\Magento\Framework\Translate\Adapter::class);
            $translator->setOptions(['translator' => $translatorCallback]);
            \Magento\Framework\Validator\AbstractValidator::setDefaultTranslator($translator);
            $this->isDefaultTranslatorInitialized = true;
        }
    }

    /**
     * Get validator config object.
     *
     * Will instantiate \Magento\Framework\Validator\Config
     *
     * @return \Magento\Framework\Validator\Config
     */
    public function getValidatorConfig()
    {
        $this->_initializeConfigList();
        $this->_initializeDefaultTranslator();
        return $this->_objectManager->create(
            \Magento\Framework\Validator\Config::class,
            ['configFiles' => $this->_configFiles]
        );
    }

    /**
     * Create validator builder instance based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @param array|null $builderConfig
     * @return \Magento\Framework\Validator\Builder
     */
    public function createValidatorBuilder($entityName, $groupName, array $builderConfig = null)
    {
        $this->_initializeDefaultTranslator();
        return $this->getValidatorConfig()->createValidatorBuilder($entityName, $groupName, $builderConfig);
    }

    /**
     * Create validator based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @param array|null $builderConfig
     * @return \Magento\Framework\Validator
     */
    public function createValidator($entityName, $groupName, array $builderConfig = null)
    {
        $this->_initializeDefaultTranslator();
        return $this->getValidatorConfig()->createValidator($entityName, $groupName, $builderConfig);
    }

    /**
     * Get serializer
     *
     * @return \Magento\Framework\Serialize\SerializerInterface
     * @deprecated 100.2.0
     */
    private function getSerializer()
    {
        if ($this->serializer === null) {
            $this->serializer = $this->_objectManager->get(
                \Magento\Framework\Serialize\SerializerInterface::class
            );
        }
        return $this->serializer;
    }

    /**
     * Get file iterator factory
     *
     * @return \Magento\Framework\Config\FileIteratorFactory
     * @deprecated 100.2.0
     */
    private function getFileIteratorFactory()
    {
        if ($this->fileIteratorFactory === null) {
            $this->fileIteratorFactory = $this->_objectManager->get(
                \Magento\Framework\Config\FileIteratorFactory::class
            );
        }
        return $this->fileIteratorFactory;
    }
}
