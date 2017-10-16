<?php
/**
 * Test for \Magento\Checkout\Controller\Index\Index
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Checkout\Test\Unit\Controller\Index;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IndexTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $onepageMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $redirectMock;

    /**
     * @var \Magento\Checkout\Controller\Index\Index
     */
    private $model;

    /**
     * @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfigMock;

    /**
     * @var \Magento\Framework\View\Page\Title
     */
    protected $titleMock;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    protected function setUp()
    {
        // mock objects
        $this->objectManager = new ObjectManager($this);
        $this->objectManagerMock = $this->basicMock(\Magento\Framework\ObjectManagerInterface::class);
        $this->dataMock = $this->basicMock(\Magento\Checkout\Helper\Data::class);
        $this->quoteMock = $this->createPartialMock(
            \Magento\Quote\Model\Quote::class,
            ['getHasError', 'hasItems', 'validateMinimumAmount', 'hasError']
        );
        $this->contextMock = $this->basicMock(\Magento\Framework\App\Action\Context::class);
        $this->sessionMock = $this->basicMock(\Magento\Customer\Model\Session::class);
        $this->onepageMock = $this->basicMock(\Magento\Checkout\Model\Type\Onepage::class);
        $this->layoutMock = $this->basicMock(\Magento\Framework\View\Layout::class);
        $this->requestMock = $this->basicMock(\Magento\Framework\App\RequestInterface::class);
        $this->responseMock = $this->basicMock(\Magento\Framework\App\ResponseInterface::class);
        $this->redirectMock = $this->basicMock(\Magento\Framework\App\Response\RedirectInterface::class);
        $this->resultPageMock = $this->basicMock(\Magento\Framework\View\Result\Page::class);
        $this->pageConfigMock = $this->basicMock(\Magento\Framework\View\Page\Config::class);
        $this->titleMock = $this->basicMock(\Magento\Framework\View\Page\Title::class);
        $this->url = $this->createMock(\Magento\Framework\UrlInterface::class);
        $this->resultRedirectMock = $this->basicMock(\Magento\Framework\Controller\Result\Redirect::class);

        $resultPageFactoryMock = $this->getMockBuilder(\Magento\Framework\View\Result\PageFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $resultPageFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->resultPageMock);

        $resultRedirectFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $resultRedirectFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->resultRedirectMock);

        // stubs
        $this->basicStub($this->onepageMock, 'getQuote')->willReturn($this->quoteMock);
        $this->basicStub($this->resultPageMock, 'getLayout')->willReturn($this->layoutMock);

        $this->basicStub($this->layoutMock, 'getBlock')
            ->willReturn($this->basicMock(\Magento\Theme\Block\Html\Header::class));
        $this->basicStub($this->resultPageMock, 'getConfig')->willReturn($this->pageConfigMock);
        $this->basicStub($this->pageConfigMock, 'getTitle')->willReturn($this->titleMock);
        $this->basicStub($this->titleMock, 'set')->willReturn($this->titleMock);

        // objectManagerMock
        $objectManagerReturns = [
            [\Magento\Checkout\Helper\Data::class, $this->dataMock],
            [\Magento\Checkout\Model\Type\Onepage::class, $this->onepageMock],
            [\Magento\Checkout\Model\Session::class, $this->basicMock(\Magento\Checkout\Model\Session::class)],
            [\Magento\Customer\Model\Session::class, $this->basicMock(\Magento\Customer\Model\Session::class)],

        ];
        $this->objectManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($objectManagerReturns));
        $this->basicStub($this->objectManagerMock, 'create')
            ->willReturn($this->basicMock(\Magento\Framework\UrlInterface::class));
        // context stubs
        $this->basicStub($this->contextMock, 'getObjectManager')->willReturn($this->objectManagerMock);
        $this->basicStub($this->contextMock, 'getRequest')->willReturn($this->requestMock);
        $this->basicStub($this->contextMock, 'getResponse')->willReturn($this->responseMock);
        $this->basicStub($this->contextMock, 'getMessageManager')
            ->willReturn($this->basicMock(\Magento\Framework\Message\ManagerInterface::class));
        $this->basicStub($this->contextMock, 'getRedirect')->willReturn($this->redirectMock);
        $this->basicStub($this->contextMock, 'getUrl')->willReturn($this->url);
        $this->basicStub($this->contextMock, 'getResultRedirectFactory')->willReturn($resultRedirectFactoryMock);

        // SUT
        $this->model = $this->objectManager->getObject(
            \Magento\Checkout\Controller\Index\Index::class,
            [
                'context' => $this->contextMock,
                'customerSession' => $this->sessionMock,
                'resultPageFactory' => $resultPageFactoryMock,
                'resultRedirectFactory' => $resultRedirectFactoryMock
            ]
        );
    }

    public function testRegenerateSessionIdOnExecute()
    {
        //Stubs to control execution flow
        $this->basicStub($this->dataMock, 'canOnepageCheckout')->willReturn(true);
        $this->basicStub($this->quoteMock, 'hasItems')->willReturn(true);
        $this->basicStub($this->quoteMock, 'getHasError')->willReturn(false);
        $this->basicStub($this->quoteMock, 'validateMinimumAmount')->willReturn(true);
        $this->basicStub($this->sessionMock, 'isLoggedIn')->willReturn(true);

        //Expected outcomes
        $this->sessionMock->expects($this->once())->method('regenerateId');
        $this->assertSame($this->resultPageMock, $this->model->execute());
    }

    public function testOnepageCheckoutNotAvailable()
    {
        $this->basicStub($this->dataMock, 'canOnepageCheckout')->willReturn(false);
        $expectedPath = 'checkout/cart';

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with($expectedPath)
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->model->execute());
    }

    public function testInvalidQuote()
    {
        $this->basicStub($this->quoteMock, 'hasError')->willReturn(true);

        $expectedPath = 'checkout/cart';
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with($expectedPath)
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->model->execute());
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $method
     *
     * @return \PHPUnit\Framework\MockObject_Builder_InvocationMocker
     */
    private function basicStub($mock, $method)
    {
        return $mock->expects($this->any())
                ->method($method)
                ->withAnyParameters();
    }

    /**
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function basicMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
