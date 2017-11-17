<?php

namespace Unit6\News\Model;

use \Magento\Framework\Model\AbstractModel;

class News extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init('Unit6\News\Model\ResourceModel\News');
    }
}
