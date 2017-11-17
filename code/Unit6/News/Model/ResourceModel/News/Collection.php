<?php

namespace Unit6\News\Model\ResourceModel\News;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var int
     */
    protected $_idFieldName = 'news_id';

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Unit6\News\Model\News', 'Unit6\News\Model\ResourceModel\News');
    }
}
