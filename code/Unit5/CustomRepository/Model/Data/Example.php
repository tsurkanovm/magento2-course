<?php

namespace Unit5\CustomRepository\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use Unit5\CustomRepository\Api\Data\ExampleInterface;

class Example extends AbstractSimpleObject implements ExampleInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_get('name');
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData('example_id', $id);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_get('example_id');
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_get('creation_time');
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData('creation_time', $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getModifiedAt()
    {
        return $this->_get('update_time');
    }

    /**
     * @inheritdoc
     */
    public function setModifiedAt($modifiedAt)
    {
        return $this->setData('update_time', $modifiedAt);
    }

    /**
     * @inheritdoc
     */
    public function setAllData(array $data)
    {
        $this->_data = $data;

        return $this;
    }


}
