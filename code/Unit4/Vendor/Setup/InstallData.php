<?php

namespace Unit4\Vendor\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Unit4\Vendor\Model\CustomConfigFactory;
use Unit4\Vendor\Model\ResourceModel\CustomConfigFactory as ResourseFactory;

class InstallData implements InstallDataInterface
{
    /* @var CustomConfigFactory*/
    private $customConfigFactory;

    /* @var ResourseFactory*/
    private $customConfigResourceFactory;

    /**
     * InstallData constructor.
     * @param CustomConfigFactory $customConfigFactory
     * @param ResourseFactory $customConfigResourceFactory
     */
    public function __construct(CustomConfigFactory $customConfigFactory, ResourseFactory $customConfigResourceFactory)
    {
        $this->customConfigFactory = $customConfigFactory;
        $this->customConfigResourceFactory = $customConfigResourceFactory;
    }

    /**
     * Function install
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $data = [
            'name' => 'Слава Україні',
            'value' => 'Героям, Слава!!!'
        ];

        $config = $this->customConfigFactory->create();
        $config->setData($data);
        $resModel = $this->customConfigResourceFactory->create();

        $resModel->save($config);
        $setup->endSetup();
    }
}
