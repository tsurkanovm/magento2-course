<?php

namespace Unit5\Material\Setup;

use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use \Magento\Catalog\Model\Product;

class InstallData implements InstallDataInterface
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
     * Function install
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'custom_material',
            [
                'label'            => 'Custom Material',
                'backend'          => ArrayBackend::class,
                'user_defined'     => 1,
                'input'            => 'multiselect',
                'source'           => Table::class,
                'type'             => 'varchar',
                'global'           => ScopedAttributeInterface::SCOPE_STORE,
                'required'         => false,
                'visible_on_front' => true,
                'attribute_set_id' => 4,
                'group'            => 'Product Details',
                'option' => [
                    'value' => [
                        'Plastic' => ['Plastic'],
                        'Steel'   => ['Steel'],
                        'Glass'   => ['Glass'],
                        'Wood'    => ['Wood'],
                        'Paper'   => ['Paper'],
                        'Silver'  => ['Silver']
                    ],
                    'order' => [
                        'Steel'   => 1,
                        'Plastic' => 2,
                        'Wood'    => 3,
                        'Glass'   => 4,
                        'Silver'  => 5,
                        'Paper'   => 6
                    ]
                ]
            ]
        );
    }
}
