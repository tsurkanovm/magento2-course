<?php

namespace Unit5\ExtensionalAttribute\Model\ResourceModel\CategoryCountry;

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
        $this->_init('Unit5\ExtensionalAttribute\Model\CategotyCountry', 'Unit5\ExtensionalAttribute\Model\ResourceModel\CategoryCountry');
    }
}
