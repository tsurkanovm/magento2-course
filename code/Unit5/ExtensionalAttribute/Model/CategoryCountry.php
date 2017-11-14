<?php

namespace Unit5\ExtensionalAttribute\Model;

use \Magento\Framework\Model\AbstractModel;
use Unit5\ExtensionalAttribute\Api\Data\CategoryCountryInterface;

class CategoryCountry extends AbstractModel implements CategoryCountryInterface
{
    /* @var \Magento\Directory\Api\CountryInformationAcquirerInterface */
    protected $countryModel;

    public function __construct(
        \Magento\Directory\Api\CountryInformationAcquirerInterfaceFactory $countryModelFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->countryModel = $countryModelFactory->create();

        parent::__construct($context, $registry, $resource, $resourceCollection);
    }
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('Unit5\ExtensionalAttribute\Model\ResourceModel\CategoryCountry');
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * @param int|mixed $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        if ($code = $this->getData('country_code')) {
            return $code;
        }

        $countryDto = $this->countryModel->getCountryInfo($this->getCountryId());
        $this->setData('country_code', $countryDto->getThreeLetterAbbreviation());

        return $countryDto->getThreeLetterAbbreviation();
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function setCountryId($id)
    {
        return $this->setData('country_id', $id);
    }

    /**
     * @return string
     */
    public function getCountryId()
    {
        return $this->getData('country_id');
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function setCatalogId($id)
    {
        return $this->setData('catalog_id', $id);
    }
}
