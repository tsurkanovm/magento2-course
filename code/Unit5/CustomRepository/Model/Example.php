<?php

namespace Unit5\CustomRepository\Model;

use \Magento\Framework\Model\AbstractModel;

class Example extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init('Unit5\CustomRepository\Model\ResourceModel\Example');
    }
}
