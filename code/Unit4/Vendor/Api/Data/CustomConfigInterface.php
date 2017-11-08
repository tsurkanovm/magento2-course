<?php
namespace Unit4\Vendor\Api\Data;

/**
 * CMS block interface.
 * @api
 * @since 100.0.2
 */
interface CustomConfigInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const CONFIG_ID      = 'id';
    const NAME         = 'name';
    const VALUE       = 'value';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME   = 'update_time';
    const IS_ACTIVE     = 'is_active';


    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return string|null
     */
    public function getValue();

    /**
     * @return $this
     * @param bool $flag
     */
    public function setIsActive(bool $flag);

}
