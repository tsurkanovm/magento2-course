<?php

namespace Unit5\CustomRepository\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Example extends AbstractDb
{


    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('training_repository_example', 'example_id');
    }


}

