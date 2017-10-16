<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Eav\Test\Unit\Model\Adminhtml\System\Config\Source;

class InputtypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype();
    }

    public function testToOptionArray()
    {
        $expectedResult = [
            ['value' => 'text', 'label' => 'Text Field'],
            ['value' => 'textarea', 'label' => 'Text Area'],
            ['value' => 'date', 'label' => 'Date'],
            ['value' => 'boolean', 'label' => 'Yes/No'],
            ['value' => 'multiselect', 'label' => 'Multiple Select'],
            ['value' => 'select', 'label' => 'Dropdown']
        ];
        $this->assertEquals($expectedResult, $this->model->toOptionArray());
    }
}
