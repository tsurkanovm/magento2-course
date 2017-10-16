<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tax\Test\Unit\Plugin\Checkout\CustomerData;

class CartTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemPriceRenderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutCart;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quote;

    /**
     * @var \Magento\Tax\Plugin\Checkout\CustomerData\Cart
     */
    protected $cart;

    protected function setUp()
    {
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->checkoutSession = $this->createMock(\Magento\Checkout\Model\Session::class);
        $this->checkoutHelper = $this->createMock(\Magento\Checkout\Helper\Data::class);
        $this->itemPriceRenderer = $this->createMock(\Magento\Tax\Block\Item\Price\Renderer::class);
        $this->checkoutCart = $this->createMock(\Magento\Checkout\CustomerData\Cart::class);
        $this->quote = $this->createMock(\Magento\Quote\Model\Quote::class);

        $this->checkoutSession->expects(
            $this->any()
        )->method(
            'getQuote'
        )->willReturn($this->quote);

        $this->cart = $helper->getObject(
            \Magento\Tax\Plugin\Checkout\CustomerData\Cart::class,
            [
                'checkoutSession' => $this->checkoutSession,
                'checkoutHelper' => $this->checkoutHelper,
                'itemPriceRenderer' => $this->itemPriceRenderer,
            ]
        );
    }

    public function testAfterGetSectionData()
    {
        $input = ['items' => [
                [
                    'item_id' => 1,
                    'product_price' => ''
                ],
                [
                    'item_id' => 2,
                    'product_price' => ''
                ],
            ]
        ];

        $this->checkoutHelper->expects(
            $this->atLeastOnce()
        )->method(
            'formatPrice'
        )->willReturn('formatted');

        $item1 = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $item2 = $this->createMock(\Magento\Quote\Model\Quote\Item::class);

        $item1->expects(
            $this->atLeastOnce()
        )->method(
            'getItemId'
        )->willReturn(1);
        $item2->expects(
            $this->atLeastOnce()
        )->method(
            'getItemId'
        )->willReturn(2);

        $this->quote->expects(
            $this->any()
        )->method(
            'getAllVisibleItems'
        )->willReturn([
            $item1,
            $item2,
        ]);

        $this->itemPriceRenderer->expects(
            $this->atLeastOnce(1)
        )->method(
            'toHtml'
        )->willReturn(1);

        $result = $this->cart->afterGetSectionData($this->checkoutCart, $input);

        $this->assertArrayHasKey('subtotal_incl_tax', $result);
        $this->assertArrayHasKey('subtotal_excl_tax', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertTrue(is_array($result['items']));
        $this->assertEquals(2, count($result['items']));
        $this->assertEquals(1, count($result['items'][0]['product_price']));
        $this->assertEquals(1, count($result['items'][1]['product_price']));
    }
}
