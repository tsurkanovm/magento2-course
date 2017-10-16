<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\UrlRewrite\Test\Unit\Block\Plugin\Store\Switcher;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class SetRedirectUrlTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Magento\UrlRewrite\Block\Plugin\Store\Switcher\SetRedirectUrl */
    protected $unit;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $urlFinder;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $urlHelper;

    /** @var \Magento\Store\Block\Switcher|\PHPUnit_Framework_MockObject_MockObject */
    protected $switcher;

    /** @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject */
    protected $store;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlBuilder;

    protected function setUp()
    {
        $this->store = $this->createMock(\Magento\Store\Model\Store::class);
        $this->request = $this->createMock(\Magento\Framework\App\Request\Http::class);
        $this->urlBuilder = $this->createMock(\Magento\Framework\UrlInterface::class);
        $this->urlHelper = $this->createMock(\Magento\Framework\Url\Helper\Data::class);
        $this->urlFinder = $this->createMock(\Magento\UrlRewrite\Model\UrlFinderInterface::class);
        $this->switcher = $this->createMock(\Magento\Store\Block\Switcher::class);

        $this->unit = (new ObjectManager($this))->getObject(
            \Magento\UrlRewrite\Block\Plugin\Store\Switcher\SetRedirectUrl::class,
            [
                'urlFinder' => $this->urlFinder,
                'urlHelper' => $this->urlHelper,
                'urlBuilder' => $this->urlBuilder,
                'request' => $this->request,
            ]
        );
    }

    public function testNoUrlRewriteForSpecificStoreOnGetTargetStorePostData()
    {
        $this->request->expects($this->once())->method('getPathInfo')->willReturn('path');
        $this->urlFinder->expects($this->once())->method('findOneByData')->willReturn(null);
        $this->urlHelper->expects($this->never())->method('getEncodedUrl');
        $this->assertEquals(
            [$this->store, []],
            $this->unit->beforeGetTargetStorePostData($this->switcher, $this->store, [])
        );
    }

    public function testTrimPathInfoForGetTargetStorePostData()
    {
        $this->request->expects($this->once())->method('getPathInfo')->willReturn('path/with/trim/');
        $this->store->expects($this->once())->method('getId')->willReturn(1);
        $this->urlFinder->expects($this->once())->method('findOneByData')
            ->with([
                \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::TARGET_PATH => 'path/with/trim',
                \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::STORE_ID => 1,
             ])
            ->willReturn(null);
        $this->urlHelper->expects($this->never())->method('getEncodedUrl');
        $this->assertEquals(
            [$this->store, []],
            $this->unit->beforeGetTargetStorePostData($this->switcher, $this->store, [])
        );
    }

    public function testGetTargetStorePostData()
    {
        $urlRewrite = $this->createMock(\Magento\UrlRewrite\Service\V1\Data\UrlRewrite::class);
        $urlRewrite->expects($this->once())->method('getRequestPath')->willReturn('path');

        $this->request->expects($this->once())->method('getPathInfo')->willReturn('path');
        $this->urlFinder->expects($this->once())->method('findOneByData')->willReturn($urlRewrite);
        $this->urlHelper->expects($this->once())->method('getEncodedUrl')->willReturn('encoded-path');
        $this->urlBuilder->expects($this->once())->method('getUrl')->willReturn('path');
        $this->assertEquals(
            [$this->store, [\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => 'encoded-path']],
            $this->unit->beforeGetTargetStorePostData($this->switcher, $this->store, [])
        );
    }
}
