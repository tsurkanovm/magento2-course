<?php
namespace Unit4\Vendor\Setup;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Unit4\Vendor\Model\CustomConfigFactory;
use Unit4\Vendor\Model\ResourceModel\CustomConfig\CollectionFactory as CollectionFactory;

/**
 * Class UpgradeData
 * @package Unit4\Vendor\Setup
 */
class UpgradeData implements UpgradeDataInterface
{

    /* @var CustomConfigFactory*/
    private $customConfigFactory;

    /* @var CollectionFactory*/
    private $confCollectionFactory;

    /* @var EntityManager*/
    private $em;

    /* @var ScopeConfigInterface*/
    private $configuration;

    /**
     * UpgradeData constructor.
     * @param CustomConfigFactory $customConfigFactory
     * @param CollectionFactory $confCollectionFactory
     * @param EntityManager $em
     * @param ScopeConfigInterface $configuration
     */
    public function __construct(CustomConfigFactory $customConfigFactory,
                                CollectionFactory $confCollectionFactory,
                                EntityManager $em,
                                ScopeConfigInterface $configuration)
    {
        $this->customConfigFactory = $customConfigFactory;
        $this->confCollectionFactory = $confCollectionFactory;
        $this->em = $em;
        $this->configuration = $configuration;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $this->addConfigOption();
        }
    }

    public function addConfigOption()
    {
        $data = [
            'name' => 'Locale',
            'value' => $this->configuration->getValue('general/locale/code')
        ];

        $config = $this->customConfigFactory->create();
        $config->setData($data);
        $this->em->save($config);
    }




    //    public function setIsActiveToFalse()
//    {
//        $collection = $this->confCollectionFactory->create();
//        /* @var $config CustomConfig */
//        foreach ($collection as $config) {
//            $config->setIsActive(false);
//            $this->em->save($config);
//        }
//    }
}
