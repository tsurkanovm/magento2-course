<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Wishlist\Test\TestCase;

use Magento\Customer\Test\Fixture\Customer;

/**
 * Test Flow:
 *
 * Preconditions:
 * 1. Customer is registered
 * 2. Product is created
 *
 * Steps:
 * 1. Login as a customer
 * 2. Navigate to catalog page
 * 3. Add created product to Wishlist according to dataset
 * 4. Perform all assertions
 *
 * @group Wishlist
 * @ZephyrId MAGETWO-29045
 */
class AddProductToWishlistEntityTest extends AbstractWishlistTest
{
    /* tags */
    const MVP = 'no';
    /* end tags */

    /**
     * Prepare data for test
     *
     * @param Customer $customer
     * @return array
     */
    public function __prepare(Customer $customer)
    {
        $customer->persist();

        return ['customer' => $customer];
    }

    /**
     * Run Add Product To Wishlist test
     *
     * @param Customer $customer
     * @param string $product
     * @param bool $configure
     * @return array
     */
    public function test(Customer $customer, $product, $configure = true)
    {
        $product = $this->createProducts($product)[0];

        // Steps:
        $this->loginCustomer($customer);
        $this->addToWishlist([$product], $configure);

        return ['product' => $product];
    }
}
