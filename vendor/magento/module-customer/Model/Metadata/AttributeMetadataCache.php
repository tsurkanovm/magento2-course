<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Model\Metadata;

use Magento\Config\App\Config\Type\System;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Eav\Model\Cache\Type;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Cache for attribute metadata
 */
class AttributeMetadataCache
{
    /**
     * Cache prefix
     */
    const ATTRIBUTE_METADATA_CACHE_PREFIX = 'ATTRIBUTE_METADATA_INSTANCES_CACHE';

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var StateInterface
     */
    private $state;

    /**
     * @var AttributeMetadataInterface[]
     */
    private $attributes;

    /**
     * @var bool
     */
    private $isAttributeCacheEnabled;

    /**
     * @var AttributeMetadataHydrator
     */
    private $attributeMetadataHydrator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor
     *
     * @param CacheInterface $cache
     * @param StateInterface $state
     * @param SerializerInterface $serializer
     * @param AttributeMetadataHydrator $attributeMetadataHydrator
     */
    public function __construct(
        CacheInterface $cache,
        StateInterface $state,
        SerializerInterface $serializer,
        AttributeMetadataHydrator $attributeMetadataHydrator
    ) {
        $this->cache = $cache;
        $this->state = $state;
        $this->serializer = $serializer;
        $this->attributeMetadataHydrator = $attributeMetadataHydrator;
    }

    /**
     * Load attributes metadata from cache
     *
     * @param string $entityType
     * @param string $suffix
     * @return AttributeMetadataInterface[]|bool
     */
    public function load($entityType, $suffix = '')
    {
        if (isset($this->attributes[$entityType . $suffix])) {
            return $this->attributes[$entityType . $suffix];
        }
        if ($this->isEnabled()) {
            $cacheKey = self::ATTRIBUTE_METADATA_CACHE_PREFIX . $entityType . $suffix;
            $serializedData = $this->cache->load($cacheKey);
            if ($serializedData) {
                $attributesData = $this->serializer->unserialize($serializedData);
                $attributes = [];
                foreach ($attributesData as $key => $attributeData) {
                    $attributes[$key] = $this->attributeMetadataHydrator->hydrate($attributeData);
                }
                $this->attributes[$entityType . $suffix] = $attributes;
                return $attributes;
            }
        }
        return false;
    }

    /**
     * Save attributes metadata to cache
     *
     * @param string $entityType
     * @param AttributeMetadataInterface[] $attributes
     * @param string $suffix
     * @return void
     */
    public function save($entityType, array $attributes, $suffix = '')
    {
        $this->attributes[$entityType . $suffix] = $attributes;
        if ($this->isEnabled()) {
            $cacheKey = self::ATTRIBUTE_METADATA_CACHE_PREFIX . $entityType . $suffix;
            $attributesData = [];
            foreach ($attributes as $key => $attribute) {
                $attributesData[$key] = $this->attributeMetadataHydrator->extract($attribute);
            }
            $serializedData = $this->serializer->serialize($attributesData);
            $this->cache->save(
                $serializedData,
                $cacheKey,
                [
                    Type::CACHE_TAG,
                    Attribute::CACHE_TAG,
                    System::CACHE_TAG
                ]
            );
        }
    }

    /**
     * Clean attributes metadata cache
     *
     * @return void
     */
    public function clean()
    {
        unset($this->attributes);
        if ($this->isEnabled()) {
            $this->cache->clean(
                [
                    Type::CACHE_TAG,
                    Attribute::CACHE_TAG,
                ]
            );
        }
    }

    /**
     * Check if cache enabled
     *
     * @return bool
     */
    private function isEnabled()
    {
        if (null === $this->isAttributeCacheEnabled) {
            $this->isAttributeCacheEnabled = $this->state->isEnabled(Type::TYPE_IDENTIFIER);
        }
        return $this->isAttributeCacheEnabled;
    }
}
