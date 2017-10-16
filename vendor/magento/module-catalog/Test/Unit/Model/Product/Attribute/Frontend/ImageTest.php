<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Test\Unit\Model\Product\Attribute\Frontend;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Frontend\Image
     */
    private $model;

    public function testGetUrl()
    {
        $this->assertEquals('catalog/product/img.jpg', $this->model->getUrl($this->getMockedProduct()));
    }

    protected function setUp()
    {
        $helper = new ObjectManager($this);
        $this->model = $helper->getObject(
            \Magento\Catalog\Model\Product\Attribute\Frontend\Image::class,
            ['storeManager' => $this->getMockedStoreManager()]
        );
        $this->model->setAttribute($this->getMockedAttribute());
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    private function getMockedProduct()
    {
        $mockBuilder = $this->getMockBuilder(\Magento\Catalog\Model\Product::class);
        $mock = $mockBuilder->setMethods(['getData', 'getStore', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue('img.jpg'));

        $mock->expects($this->any())
            ->method('getStore');

        return $mock;
    }

    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    private function getMockedStoreManager()
    {
        $mockedStore = $this->getMockedStore();

        $mockBuilder = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class);
        $mock = $mockBuilder->setMethods(['getStore'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $mock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($mockedStore));

        return $mock;
    }

    /**
     * @return \Magento\Store\Model\Store
     */
    private function getMockedStore()
    {
        $mockBuilder = $this->getMockBuilder(\Magento\Store\Model\Store::class);
        $mock = $mockBuilder->setMethods(['getBaseUrl', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $mock->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue(''));

        return $mock;
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    private function getMockedAttribute()
    {
        $mockBuilder = $this->getMockBuilder(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute::class);
        $mockBuilder->setMethods(['getAttributeCode', '__wakeup']);
        $mockBuilder->disableOriginalConstructor();
        $mock = $mockBuilder->getMockForAbstractClass();

        $mock->expects($this->any())
            ->method('getAttributeCode');

        return $mock;
    }
}
