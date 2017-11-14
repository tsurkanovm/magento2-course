<?php

namespace Unit5\ExtensionalAttribute\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CategoryCountry extends AbstractDb
{


    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('category_countries', 'id');
    }


}

