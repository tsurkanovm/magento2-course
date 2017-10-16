<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Shipping\Test\Unit\Controller\Adminhtml\Order\Shipment;

use Magento\Backend\App\Action;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
/**
 * Class AddTrackTest
 *
 * @package Magento\Shipping\Controller\Adminhtml\Order\Shipment
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddTrackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentLoader;

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\Shipment\AddTrack
     */
    protected $controller;

    /**
     * @var Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var  \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

    /**
     * @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfigMock;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageTitleMock;

    protected function setUp()
    {
        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->shipmentLoader = $this->getMockBuilder(
            \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader::class)
            ->disableOriginalConstructor()
            ->setMethods(['setShipmentId', 'setOrderId', 'setShipment', 'setTracking', 'load'])
            ->getMock();
        $this->context = $this->createPartialMock(\Magento\Backend\App\Action\Context::class, [
                'getRequest',
                'getResponse',
                'getRedirect',
                'getObjectManager',
                'getTitle',
                'getView'
            ]);
        $this->response = $this->createPartialMock(\Magento\Framework\App\ResponseInterface::class, ['setRedirect', 'sendResponse', 'setBody']);
        $this->request = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->disableOriginalConstructor()->getMock();
        $this->objectManager = $this->createPartialMock(\Magento\Framework\ObjectManager\ObjectManager::class, ['create', 'get']);
        $this->view = $this->createMock(\Magento\Framework\App\ViewInterface::class);
        $this->resultPageMock = $this->getMockBuilder(\Magento\Framework\View\Result\Page::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfigMock = $this->getMockBuilder(\Magento\Framework\View\Page\Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTitleMock = $this->getMockBuilder(\Magento\Framework\View\Page\Title::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->context->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->response));
        $this->context->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $this->context->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($this->view));
        $this->controller = $objectManagerHelper->getObject(
            \Magento\Shipping\Controller\Adminhtml\Order\Shipment\AddTrack::class,
            [
                'context' => $this->context,
                'shipmentLoader' => $this->shipmentLoader,
                'request' => $this->request,
                'response' => $this->response,
                'view' => $this->view
            ]
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecute()
    {
        $carrier = 'carrier';
        $number = 'number';
        $title = 'title';
        $shipmentId = 1000012;
        $orderId = 10003;
        $tracking = [];
        $shipmentData = ['items' => [], 'send_email' => ''];
        $shipment = $this->createPartialMock(\Magento\Sales\Model\Order\Shipment::class, ['addTrack', '__wakeup', 'save']);
        $this->request->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap(
                    [
                        ['order_id', null, $orderId], ['shipment_id', null, $shipmentId],
                        ['shipment', null, $shipmentData], ['tracking', null, $tracking],
                    ]
                )
            );
        $this->request->expects($this->any())
            ->method('getPost')
            ->will(
                $this->returnValueMap(
                    [
                        ['carrier', null, $carrier],
                        ['number', null, $number],
                        ['title', null, $title],
                    ]
                )
            );
        $this->shipmentLoader->expects($this->any())
            ->method('setShipmentId')
            ->with($shipmentId);
        $this->shipmentLoader->expects($this->any())
            ->method('setOrderId')
            ->with($orderId);
        $this->shipmentLoader->expects($this->any())
            ->method('setShipment')
            ->with($shipmentData);
        $this->shipmentLoader->expects($this->any())
            ->method('setTracking')
            ->with($tracking);
        $this->shipmentLoader->expects($this->once())
            ->method('load')
            ->will($this->returnValue($shipment));
        $track = $this->getMockBuilder(\Magento\Sales\Model\Order\Shipment\Track::class)
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'setNumber', 'setCarrierCode', 'setTitle'])
            ->getMock();
        $this->objectManager->expects($this->atLeastOnce())
            ->method('create')
            ->with(\Magento\Sales\Model\Order\Shipment\Track::class)
            ->will($this->returnValue($track));
        $track->expects($this->once())
            ->method('setNumber')
            ->with($number)
            ->will($this->returnSelf());
        $track->expects($this->once())
            ->method('setCarrierCode')
            ->with($carrier)
            ->will($this->returnSelf());
        $track->expects($this->once())
            ->method('setTitle')
            ->with($title)
            ->will($this->returnSelf());
        $this->view->expects($this->once())
            ->method('loadLayout')
            ->will($this->returnSelf());
        $layout = $this->createMock(\Magento\Framework\View\LayoutInterface::class);
        $menuBlock = $this->createPartialMock(\Magento\Framework\View\Element\BlockInterface::class, ['toHtml']);
        $html = 'html string';
        $this->view->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layout));
        $layout->expects($this->once())
            ->method('getBlock')
            ->with('shipment_tracking')
            ->will($this->returnValue($menuBlock));
        $menuBlock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($html));
        $shipment->expects($this->once())
            ->method('addTrack')
            ->with($this->equalTo($track))
            ->will($this->returnSelf());
        $shipment->expects($this->any())
            ->method('save')
            ->will($this->returnSelf());
        $this->view->expects($this->any())
            ->method('getPage')
            ->willReturn($this->resultPageMock);
        $this->resultPageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->pageConfigMock);
        $this->pageConfigMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($this->pageTitleMock);
        $this->response->expects($this->once())
            ->method('setBody')
            ->with($html);
        $this->assertNull($this->controller->execute());
    }
}
