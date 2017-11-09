<?php
namespace Unit4\EAV\Setup;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Unit4\Eav\Entity\Attribute\Frontend\HtmlList;
use Unit4\Eav\Entity\Attribute\Source\CustomerPriority;

/**
 * Class UpgradeData
 * @package Unit4\EAV\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
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
        if (version_compare('0.1.0', $context->getVersion(), '>=')) {
            $this->createMultiSelectProductAttribute($setup);
        }

        if (version_compare('0.1.1', $context->getVersion(), '>=')) {
            $this->updateFrontViewForFlavorAttr($setup);
        }

        if (version_compare('0.1.2', $context->getVersion(), '>=')) {
            $this->addCustomerAttr($setup);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function createMultiSelectProductAttribute(ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'flavor_multiselect',
            [
                'label'            => 'Flavor multiselect',
                'backend'          => ArrayBackend::class,
                'user_defined'     => 1,
                'input'            => 'multiselect',
                'source'           => Table::class,
                'type'             => 'varchar',
                'global'           => ScopedAttributeInterface::SCOPE_STORE,
                'required'         => false,
                'visible_on_front' => true,
                'attribute_set_id' => 15,
                'group'            => 'Product Details',
                'option' => [
                    'value' => [
                        'vanilla'    => ['vanilla'],
                        'smelly'     => ['very smelly'],
                        'strawberry' => ['strawberry and flowers'],
                        'perfume'    => ['perfume']
                    ],
                    'order' => [
                        'perfume'    => 1,
                        'smelly'     => 2,
                        'strawberry' => 3,
                        'vanilla'    => 4
                    ],
            ]
        ]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function updateFrontViewForFlavorAttr(ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->updateAttribute(
            Product::ENTITY,
            'flavor_multiselect',
            [
                'frontend_model' => HtmlList::class,
                'is_html_allowed_on_front' => 1
            ]
        );

    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function addCustomerAttr(ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Customer::ENTITY,
            'priority',
            [
                'label' => 'Priority',
                'visible' => true,
                'type' => 'int',
                'input' => 'select',
                'source' => CustomerPriority::class,
                'required' => 0,
                'system' => 0,
                'position' => 100,
                'adminhtml_only' => 1
            ]
        );
    }
}
