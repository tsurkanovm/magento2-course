<?php

namespace Unit5\ExtensionalAttribute\Api\Data;


interface CategoryCountryInterface
{
    /**
     * @return string
     */
    public function getCountryCode();

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
     * @param string $id
     *
     * @return $this
     */
    public function setCountryId($id);

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setCatalogId($id);

}
