<?php

namespace Unit5\ExtensionalAttribute\Plugin\Model;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResourceModel;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Unit5\ExtensionalAttribute\Model\CategoryCountry;
use Unit5\ExtensionalAttribute\Model\ResourceModel\CategoryCountry as CategoryCountryResource;

class SetCountriesPlugin
{
    /* @var ExtensionAttributesFactory */
    protected $exFactory;

    /* @var CategoryCountry */
    protected $countriesModel;

    /* @var CategoryCountryResource */
    protected $countriesRM;

    /**
     * @param ExtensionAttributesFactory $exFactory
     * @param CategoryCountry $countriesModel
     * @param CategoryCountryResource $countriesRM
     */
    public function __construct(ExtensionAttributesFactory $exFactory,
                                CategoryCountry $countriesModel,
                                CategoryCountryResource $countriesRM)
    {
        $this->exFactory = $exFactory;
        $this->countriesModel = $countriesModel;
        $this->countriesRM = $countriesRM;
    }


    /**
     * @param CategoryResourceModel $subject
     * @param CategoryResourceModel $result
     * @param Category $category
     * @param $entityId
     * @return CategoryResourceModel
     */
    public function afterLoad($subject, $result, Category $category, $entityId)
    {
        $categoryExtension = $category->getExtensionAttributes();

        if ($categoryExtension === null) {
            $categoryExtension = $this->exFactory->create(Category::class);
        }

        $this->countriesRM->load($this->countriesModel, $entityId, 'category_id');
        $categoryExtension->setCountries($this->countriesModel);

        $category->setExtensionAttributes($categoryExtension);

        return $result;
    }
}
