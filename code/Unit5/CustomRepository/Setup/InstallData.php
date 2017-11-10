<?php

namespace Unit5\CustomRepository\Setup;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Unit5\CustomRepository\Model\Data\ExampleFactory;
use Unit5\CustomRepository\Model\Data\Example;

class InstallData implements InstallDataInterface
{
    /* @var ExampleFactory*/
    private $exampleFactory;

    /* @var EntityManager*/
    private $em;

    /**
     * InstallData constructor.
     * @param ExampleFactory $exampleFactory
     * @param EntityManager $em
     */
    public function __construct(ExampleFactory $exampleFactory, EntityManager $em)
    {
        $this->exampleFactory = $exampleFactory;
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
            ['name' => 'Foo'],
            ['name' => 'Bar'],
            ['name' => 'Baz'],
            ['name' => 'Qux']
        ];

        foreach ($data as $item){
            /* @var $exData Example*/
            $exData = $this->exampleFactory->create();
            $exData->setAllData($item);
            $this->em->save($exData);
        }
    }
}
