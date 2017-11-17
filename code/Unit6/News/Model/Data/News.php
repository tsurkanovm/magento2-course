<?php

namespace Unit6\News\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use Unit5\CustomRepository\Api\Data\ExampleInterface;
use Unit6\News\Api\Data\NewsInterface;

class News extends AbstractSimpleObject implements NewsInterface
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->_get('title');
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData('news_id', $id);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_get('news_id');
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        return $this->setData('title', $title);
    }

    /**
     * @inheritdoc
     */
    public function getContent()
    {
        return $this->_get('content');
    }

    /**
     * @inheritdoc
     */
    public function setContent($content)
    {
        return $this->setData('content', $content);
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
