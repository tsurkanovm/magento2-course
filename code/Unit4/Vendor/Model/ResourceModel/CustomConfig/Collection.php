<?php

namespace Unit4\Vendor\Model\ResourceModel\CustomConfig;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Unit4\Vendor\Model\CustomConfig', 'Unit4\Vendor\Model\ResourceModel\CustomConfig');
    }
}
