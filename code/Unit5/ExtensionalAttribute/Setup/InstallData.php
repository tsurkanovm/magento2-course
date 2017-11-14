<?php

namespace Unit5\ExtensionalAttribute\Setup;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Psr\Log\LoggerInterface;
use Unit5\ExtensionalAttribute\Model\CategoryCountry;
use Unit5\ExtensionalAttribute\Model\CategoryCountryFactory;

class InstallData implements InstallDataInterface
{
    /* @var CategoryCountryFactory*/
    private $modelFactory;

    /* @var EntityManager*/
    private $em;

    /**
     * InstallData constructor.
     * @param CategoryCountryFactory $modelFactory
     * @param EntityManager $em
     */
    public function __construct(CategoryCountryFactory $modelFactory, EntityManager $em)
    {
        $this->modelFactory = $modelFactory;
        $this->em = $em;
    }

    /**
     * Function install
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $data = [
            ['country_id' => 'AZ', 'category_id' => 1],
            ['country_id' => 'BE', 'category_id' => 2],
            ['country_id' => 'BR', 'category_id' => 3]
        ];

        foreach ($data as $item){
            /* @var $mData CategoryCountry*/
            $mData = $this->modelFactory->create();
            $mData->setData($item);
            $this->em->save($mData);
        }
    }
}
