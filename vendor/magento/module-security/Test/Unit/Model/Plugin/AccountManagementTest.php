<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Security\Test\Unit\Model\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test class for \Magento\Security\Model\Plugin\AccountManagement testing
 */
class AccountManagementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var  \Magento\Security\Model\Plugin\AccountManagement
     */
    protected $model;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Security\Model\SecurityManager
     */
    protected $securityManager;

    /**
     * @var \Magento\Customer\Model\AccountManagement
     */
    protected $accountManagement;

    /**
     * @var  \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * Init mocks for tests
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->request =  $this->createMock(\Magento\Framework\App\RequestInterface::class);

        $this->securityManager = $this->createPartialMock(
            \Magento\Security\Model\SecurityManager::class,
            ['performSecurityCheck']
        );

        $this->accountManagement =  $this->createMock(\Magento\Customer\Model\AccountManagement::class);

        $this->model = $this->objectManager->getObject(
            \Magento\Security\Model\Plugin\AccountManagement::class,
            [
                'request' => $this->request,
                'securityManager' => $this->securityManager
            ]
        );
    }

    /**
     * @return void
     */
    public function testBeforeInitiatePasswordReset()
    {
        $email = 'test@example.com';
        $template = \Magento\Customer\Model\AccountManagement::EMAIL_RESET;

        $this->securityManager->expects($this->once())
            ->method('performSecurityCheck')
            ->with(\Magento\Security\Model\PasswordResetRequestEvent::CUSTOMER_PASSWORD_RESET_REQUEST, $email)
            ->willReturnSelf();

        $this->model->beforeInitiatePasswordReset(
            $this->accountManagement,
            $email,
            $template
        );
    }
}
