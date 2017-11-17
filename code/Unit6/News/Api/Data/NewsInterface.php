<?php

namespace Unit6\News\Api\Data;


interface NewsInterface
{
    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string|null
     */
    public function getTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string|null
     */
    public function getContent();

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getModifiedAt();

    /**
     * @param string $modifiedAt
     * @return $this
     */
    public function setModifiedAt($modifiedAt);

    /**
     * @param array $data
     * @return $this
     */
    public function setAllData(array $data);

}
