<?php

namespace Unit6\News\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class News extends AbstractDb
{


    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('unit6_news', 'news_id');
    }


}

