<?php

namespace Unit4\Vendor\Setup;

use Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter;
use Magento\Catalog\Model\ResourceModel\Product\Gallery;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    const VENDOR_CONFIG_TABLE_NAME = 'unit4_config';

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $this->addIsActiveColumn($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addIsActiveColumn(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();

        if (!$setup->tableExists(self::VENDOR_CONFIG_TABLE_NAME) ||
            $connection->tableColumnExists(self::VENDOR_CONFIG_TABLE_NAME, 'is_active')) {
            return;
        }

        $connection->addColumn(
            self::VENDOR_CONFIG_TABLE_NAME,
            'is_active',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                'comment' => 'Defined active or not current config option',
            ]
        );
    }
}
