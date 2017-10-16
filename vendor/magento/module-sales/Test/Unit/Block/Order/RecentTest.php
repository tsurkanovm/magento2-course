<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Test\Unit\Block\Order;

class RecentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Sales\Block\Order\Recent
     */
    protected $block;

    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderConfig;

    protected function setUp()
    {
        $this->context = $this->createMock(\Magento\Framework\View\Element\Template\Context::class);
        $this->orderCollectionFactory = $this->createPartialMock(
            \Magento\Sales\Model\ResourceModel\Order\CollectionFactory::class,
            ['create']
        );
        $this->customerSession = $this->createPartialMock(\Magento\Customer\Model\Session::class, ['getCustomerId']);
        $this->orderConfig = $this->createPartialMock(
            \Magento\Sales\Model\Order\Config::class,
            ['getVisibleOnFrontStatuses']
        );
    }

    public function testConstructMethod()
    {
        $data = [];
        $attribute = ['customer_id', 'status'];
        $customerId = 25;
        $layout = $this->createPartialMock(\Magento\Framework\View\Layout::class, ['getBlock']);
        $this->context->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layout));
        $this->customerSession->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));

        $statuses = ['pending', 'processing', 'complete'];
        $this->orderConfig->expects($this->once())
            ->method('getVisibleOnFrontStatuses')
            ->will($this->returnValue($statuses));

        $orderCollection = $this->createPartialMock(\Magento\Sales\Model\ResourceModel\Order\Collection::class, [
                'addAttributeToSelect',
                'addFieldToFilter',
                'addAttributeToFilter',
                'addAttributeToSort',
                'setPageSize',
                'load'
            ]);
        $this->orderCollectionFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($orderCollection));
        $orderCollection->expects($this->at(0))
            ->method('addAttributeToSelect')
            ->with($this->equalTo('*'))
            ->will($this->returnSelf());
        $orderCollection->expects($this->at(1))
            ->method('addAttributeToFilter')
            ->with($attribute[0], $this->equalTo($customerId))
            ->willReturnSelf();
        $orderCollection->expects($this->at(2))
            ->method('addAttributeToFilter')
            ->with($attribute[1], $this->equalTo(['in' => $statuses]))
            ->will($this->returnSelf());
        $orderCollection->expects($this->at(3))
            ->method('addAttributeToSort')
            ->with('created_at', 'desc')
            ->will($this->returnSelf());
        $orderCollection->expects($this->at(4))
            ->method('setPageSize')
            ->with('5')
            ->will($this->returnSelf());
        $orderCollection->expects($this->at(5))
            ->method('load')
            ->will($this->returnSelf());
        $this->block = new \Magento\Sales\Block\Order\Recent(
            $this->context,
            $this->orderCollectionFactory,
            $this->customerSession,
            $this->orderConfig,
            $data
        );
        $this->assertEquals($orderCollection, $this->block->getOrders());
    }
}
