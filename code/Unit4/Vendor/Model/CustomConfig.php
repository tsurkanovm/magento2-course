<?php

namespace Unit4\Vendor\Model;

use Unit4\Vendor\Api\Data\CustomConfigInterface;
use \Magento\Framework\Model\AbstractModel;

class CustomConfig extends AbstractModel implements CustomConfigInterface
{
    const CACHE_TAG = 'unit4_config';

    protected $_cacheTag = 'unit4_config';

    protected $_eventPrefix = 'unit4_config';

    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init('Unit4\Vendor\Model\ResourceModel\CustomConfig');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @param bool $flag
     * @return $this|null
     */
    public function setIsActive(bool $flag)
    {
        $this->setData(self::IS_ACTIVE, (int) $flag);

        return $this;
    }
}
