<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Directory\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Directory\Helper\Data;

/**
 * Upgrade Data script for Directory module.
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Directory data.
     *
     * @var Data
     */
    private $directoryData;

    /**
     * @param Data $directoryData
     */
    public function __construct(Data $directoryData)
    {
        $this->directoryData = $directoryData;
    }

    /**
     * Upgrades data for Directry module.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->addCroatia($setup);
        }
    }

    /**
     * Add Croatia and it's states to appropriate tables.
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function addCroatia($setup)
    {
        /**
         * Fill table directory/country_region
         * Fill table directory/country_region_name for en_US locale
         */
        $data = [
            ['HR', 'HR-01', 'Zagrebačka županija'],
            ['HR', 'HR-02', 'Krapinsko-zagorska županija'],
            ['HR', 'HR-03', 'Sisačko-moslavačka županija'],
            ['HR', 'HR-04', 'Karlovačka županija'],
            ['HR', 'HR-05', 'Varaždinska županija'],
            ['HR', 'HR-06', 'Koprivničko-križevačka županija'],
            ['HR', 'HR-07', 'Bjelovarsko-bilogorska županija'],
            ['HR', 'HR-08', 'Primorsko-goranska županija'],
            ['HR', 'HR-09', 'Ličko-senjska županija'],
            ['HR', 'HR-10', 'Virovitičko-podravska županija'],
            ['HR', 'HR-11', 'Požeško-slavonska županija'],
            ['HR', 'HR-12', 'Brodsko-posavska županija'],
            ['HR', 'HR-13', 'Zadarska županija'],
            ['HR', 'HR-14', 'Osječko-baranjska županija'],
            ['HR', 'HR-15', 'Šibensko-kninska županija'],
            ['HR', 'HR-16', 'Vukovarsko-srijemska županija'],
            ['HR', 'HR-17', 'Splitsko-dalmatinska županija'],
            ['HR', 'HR-18', 'Istarska županija'],
            ['HR', 'HR-19', 'Dubrovačko-neretvanska županija'],
            ['HR', 'HR-20', 'Međimurska županija'],
            ['HR', 'HR-21', 'Grad Zagreb']
        ];
        foreach ($data as $row) {
            $bind = ['country_id' => $row[0], 'code' => $row[1], 'default_name' => $row[2]];
            $setup->getConnection()->insert($setup->getTable('directory_country_region'), $bind);
            $regionId = $setup->getConnection()->lastInsertId($setup->getTable('directory_country_region'));
            $bind = ['locale' => 'en_US', 'region_id' => $regionId, 'name' => $row[2]];
            $setup->getConnection()->insert($setup->getTable('directory_country_region_name'), $bind);
        }
        /**
         * Upgrade core_config_data general/region/state_required field.
         */
        $countries = $this->directoryData->getCountryCollection()->getCountriesWithRequiredStates();
        $setup->getConnection()->update(
            $setup->getTable('core_config_data'),
            [
                'value' => implode(',', array_keys($countries))
            ],
            [
                'scope="default"',
                'scope_id=0',
                'path=?' => Data::XML_PATH_STATES_REQUIRED
            ]
        );
    }
}
