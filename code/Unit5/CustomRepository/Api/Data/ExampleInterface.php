<?php

namespace Unit5\CustomRepository\Api\Data;


interface ExampleInterface
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
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

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
