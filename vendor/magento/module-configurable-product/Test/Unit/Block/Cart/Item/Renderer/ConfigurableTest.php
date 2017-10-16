<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ConfigurableProduct\Test\Unit\Block\Cart\Item\Renderer;

use Magento\Catalog\Model\Config\Source\Product\Thumbnail as ThumbnailSource;
use Magento\ConfigurableProduct\Block\Cart\Item\Renderer\Configurable as Renderer;

class ConfigurableTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Magento\Framework\View\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_configManager;

    /** @var \Magento\Catalog\Helper\Image|\PHPUnit_Framework_MockObject_MockObject */
    protected $_imageHelper;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_scopeConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $productConfigMock;

    /** @var Renderer */
    protected $_renderer;

    protected function setUp()
    {
        parent::setUp();
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_configManager = $this->createMock(\Magento\Framework\View\ConfigInterface::class);
        $this->_imageHelper = $this->createPartialMock(
            \Magento\Catalog\Helper\Image::class,
            ['init', 'resize', '__toString']
        );
        $this->_scopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $this->productConfigMock = $this->createMock(\Magento\Catalog\Helper\Product\Configuration::class);
        $this->_renderer = $objectManagerHelper->getObject(
            \Magento\ConfigurableProduct\Block\Cart\Item\Renderer\Configurable::class,
            [
                'viewConfig' => $this->_configManager,
                'imageHelper' => $this->_imageHelper,
                'scopeConfig' => $this->_scopeConfig,
                'productConfig' => $this->productConfigMock
            ]
        );
    }

    /**
     * Child thumbnail is available and config option is not set to use parent thumbnail.
     */
    public function testGetProductForThumbnail()
    {
        $childHasThumbnail = true;
        $useParentThumbnail = false;
        $products = $this->_initProducts($childHasThumbnail, $useParentThumbnail);

        $productForThumbnail = $this->_renderer->getProductForThumbnail();
        $this->assertSame(
            $products['childProduct'],
            $productForThumbnail,
            'Child product was expected to be returned.'
        );
    }

    /**
     * Child thumbnail is not available and config option is not set to use parent thumbnail.
     */
    public function testGetProductForThumbnailChildThumbnailNotAvailable()
    {
        $childHasThumbnail = false;
        $useParentThumbnail = false;
        $products = $this->_initProducts($childHasThumbnail, $useParentThumbnail);

        $productForThumbnail = $this->_renderer->getProductForThumbnail();
        $this->assertSame(
            $products['parentProduct'],
            $productForThumbnail,
            'Parent product was expected to be returned.'
        );
    }

    /**
     * Child thumbnail is available and config option is set to use parent thumbnail.
     */
    public function testGetProductForThumbnailConfigUseParent()
    {
        $childHasThumbnail = true;
        $useParentThumbnail = true;
        $products = $this->_initProducts($childHasThumbnail, $useParentThumbnail);

        $productForThumbnail = $this->_renderer->getProductForThumbnail();
        $this->assertSame(
            $products['parentProduct'],
            $productForThumbnail,
            'Parent product was expected to be returned ' .
            'if "checkout/cart/configurable_product_image option" is set to "parent" in system config.'
        );
    }

    /**
     * Initialize parent configurable product and child product.
     *
     * @param bool $childHasThumbnail
     * @param bool $useParentThumbnail
     * @return \Magento\Catalog\Model\Product[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    protected function _initProducts($childHasThumbnail = true, $useParentThumbnail = false)
    {
        /** Set option which can force usage of parent product thumbnail when configurable product is displayed */
        $thumbnailToBeUsed = $useParentThumbnail
            ? ThumbnailSource::OPTION_USE_PARENT_IMAGE
            : ThumbnailSource::OPTION_USE_OWN_IMAGE;
        $this->_scopeConfig->expects(
            $this->any()
        )->method(
            'getValue'
        )->with(
            Renderer::CONFIG_THUMBNAIL_SOURCE
        )->will(
            $this->returnValue($thumbnailToBeUsed)
        );

        /** Initialized parent product */
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $parentProduct */
        $parentProduct = $this->createMock(\Magento\Catalog\Model\Product::class);

        /** Initialize child product */
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $childProduct */
        $childProduct = $this->createPartialMock(\Magento\Catalog\Model\Product::class, ['getThumbnail', '__wakeup']);
        $childThumbnail = $childHasThumbnail ? 'thumbnail.jpg' : 'no_selection';
        $childProduct->expects($this->any())->method('getThumbnail')->will($this->returnValue($childThumbnail));

        /** Mock methods which return parent and child products */
        /** @var \Magento\Quote\Model\Quote\Item\Option|\PHPUnit_Framework_MockObject_MockObject $itemOption */
        $itemOption = $this->createMock(\Magento\Quote\Model\Quote\Item\Option::class);
        $itemOption->expects($this->any())->method('getProduct')->will($this->returnValue($childProduct));
        /** @var \Magento\Quote\Model\Quote\Item|\PHPUnit_Framework_MockObject_MockObject $item */
        $item = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $item->expects($this->any())->method('getProduct')->will($this->returnValue($parentProduct));
        $item->expects(
            $this->any()
        )->method(
            'getOptionByCode'
        )->with(
            'simple_product'
        )->will(
            $this->returnValue($itemOption)
        );
        $this->_renderer->setItem($item);

        return ['parentProduct' => $parentProduct, 'childProduct' => $childProduct];
    }

    public function testGetOptionList()
    {
        $itemMock = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $this->_renderer->setItem($itemMock);
        $this->productConfigMock->expects($this->once())->method('getOptions')->with($itemMock);
        $this->_renderer->getOptionList();
    }

    public function testGetIdentities()
    {
        $productTags = ['catalog_product_1'];
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $product->expects($this->exactly(2))->method('getIdentities')->will($this->returnValue($productTags));
        $item = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $item->expects($this->exactly(2))->method('getProduct')->will($this->returnValue($product));
        $this->_renderer->setItem($item);
        $this->assertEquals(array_merge($productTags, $productTags), $this->_renderer->getIdentities());
    }
}
