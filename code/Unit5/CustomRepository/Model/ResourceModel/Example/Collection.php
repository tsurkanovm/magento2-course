<?php

namespace Unit5\CustomRepository\Model\ResourceModel\Example;

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
        $this->_init('Unit5\CustomRepository\Model\Example',
            'Unit5\CustomRepository\Model\ResourceModel\Example');
    }
}
