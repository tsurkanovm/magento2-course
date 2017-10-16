<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Authorizenet\Test\Block\Form;

use Magento\Mtf\Client\Locator;
use Magento\Payment\Test\Block\Form\PaymentCc;

/**
 * Form for credit card data for Authorize.net payment method.
 */
class AuthorizenetCc extends PaymentCc
{
    /**
     * Authorizenet form locators.
     *
     * @var array
     */
    private $authorizenetForm = [
        "cc_number" => "//*[@id='authorizenet_directpost_cc_number']",
        "cc_exp_month" => "//*[@id='authorizenet_directpost_expiration']",
        "cc_exp_year" => "//*[@id='authorizenet_directpost_expiration_yr']",
        "cc_cid" => "//*[@id='authorizenet_directpost_cc_cid']",
    ];

    /**
     * Get Filled CC Number.
     *
     * @return string
     */
    public function getCCNumber()
    {
        return $this->_rootElement->find($this->authorizenetForm['cc_number'], Locator::SELECTOR_XPATH)->getValue();
    }

    /**
     * Get Filled CC Number.
     *
     * @return string
     */
    public function getExpMonth()
    {
        return $this->_rootElement->find($this->authorizenetForm['cc_exp_month'], Locator::SELECTOR_XPATH)->getValue();
    }

    /**
     * Get Expiration Year
     *
     * @return string
     */
    public function getExpYear()
    {
        return $this->_rootElement->find($this->authorizenetForm['cc_exp_year'], Locator::SELECTOR_XPATH)->getValue();
    }

    /**
     * Get CID
     *
     * @return string
     */
    public function getCid()
    {
        return $this->_rootElement->find($this->authorizenetForm['cc_cid'], Locator::SELECTOR_XPATH)->getValue();
    }
}
