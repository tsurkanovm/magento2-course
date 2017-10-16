<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Integration\Test\Unit\Model;

/**
 * Unit test for \Magento\Integration\Model\CredentialsValidator
 */
class CredentialsValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Integration\Model\CredentialsValidator
     */
    protected $credentialsValidator;

    protected function setUp()
    {
        $this->credentialsValidator = new \Magento\Integration\Model\CredentialsValidator();
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage username is a required field.
     */
    public function testValidateNoUsername()
    {
        $username = '';
        $password = 'my_password';

        $this->credentialsValidator->validate($username, $password);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage password is a required field.
     */
    public function testValidateNoPassword()
    {
        $username = 'my_username';
        $password = '';

        $this->credentialsValidator->validate($username, $password);
    }

    public function testValidateValidCredentials()
    {
        $username = 'my_username';
        $password = 'my_password';

        $result = $this->credentialsValidator->validate($username, $password);
        $this->assertNull($result);
    }
}
