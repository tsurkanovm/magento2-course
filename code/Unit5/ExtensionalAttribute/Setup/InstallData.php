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

    /* @var LoggerInterface*/
    private $logger;

    /**
     * InstallData constructor.
     * @param CategoryCountryFactory $modelFactory
     * @param EntityManager $em
     * @param LoggerInterface $logger
     */
    public function __construct(CategoryCountryFactory $modelFactory, EntityManager $em, LoggerInterface $logger)
    {
        $this->modelFactory = $modelFactory;
        $this->em = $em;
        $this->logger = $logger;
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
            ['country_id' => 'BR', 'category_id' => 3],
//            ['country_id' => 'CA', 'category_id' => 4],
//            ['country_id' => 'FR', 'category_id' => 5],
//            ['country_id' => 'ID', 'category_id' => 6],
//            ['country_id' => 'IT', 'category_id' => 7],
//            ['country_id' => 'KZ', 'category_id' => 8]
        ];

        foreach ($data as $item){
            /* @var $mData CategoryCountry*/
            $mData = $this->modelFactory->create();
            $mData->setData($item);
//            $this->logger->debug('Model - ' . json_encode($mData->toArray()));
//            $this->logger->debug('Items - ' . json_encode($item));
            $this->em->save($mData);
        }
    }
}
