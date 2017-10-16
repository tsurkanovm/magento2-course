<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Model\Product;

use \Magento\Bundle\Api\Data\LinkInterface;

/**
 * @magentoDataFixture Magento/Bundle/_files/PriceCalculator/fixed_bundle_product_with_special_price.php
 * @magentoAppArea frontend
 */
class FixedBundleWithSpecialPriceCalculatorTest extends BundlePriceAbstract
{
    /**
     * @param array $strategyModifiers
     * @param array $expectedResults
     * @dataProvider getTestCases
     * @magentoAppIsolation enabled
     */
    public function testPriceForFixedBundle(array $strategyModifiers, array $expectedResults)
    {
        $this->prepareFixture($strategyModifiers, 'bundle_product');
        $bundleProduct = $this->productRepository->get('bundle_product', false, null, true);

        /** @var \Magento\Framework\Pricing\PriceInfo\Base $priceInfo */
        $priceInfo = $bundleProduct->getPriceInfo();
        $priceCode = \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE;

        $priceInfoFromIndexer = $this->productCollectionFactory->create()
            ->addFieldToFilter('sku', 'bundle_product')
            ->addPriceData()
            ->load()
            ->getFirstItem();
        $this->assertEquals(
            $expectedResults['minimalPrice'],
            $priceInfo->getPrice($priceCode)->getMinimalPrice()->getValue(),
            'Failed to check minimal price on product'
        );

        $this->assertEquals(
            $expectedResults['maximalPrice'],
            $priceInfo->getPrice($priceCode)->getMaximalPrice()->getValue(),
            'Failed to check maximal price on product'
        );
        $this->assertEquals($expectedResults['indexerMinimalPrice'], $priceInfoFromIndexer->getMinimalPrice());
    }

    /**
     * Test cases for current test
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getTestCases()
    {
        return [
            '
                #1 Testing price for fixed bundle product
                with special price and without any sub items and options
            ' => [
                'strategy' => $this->getBundleConfiguration1(),
                'expectedResults' => [
                    // 110 * 0.5
                    'minimalPrice' => 55,

                    // 110 * 0.5
                    'maximalPrice' => 55,
                    'indexerMinimalPrice' => null
                ]
            ],

            '
                #2 Testing price for fixed bundle product
                with special price, fixed sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration2(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 20) + 100
                    'minimalPrice' => 165,

                    // 0.5 * (110 + 1 * 20) + 100
                    'maximalPrice' => 165,
                    'indexerMinimalPrice' => 130
                ]
            ],

            '
                #3 Testing price for fixed bundle product
                with special price, percent sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration2(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 110 * 0.2 + 110 * 1)
                    'minimalPrice' => 121,

                    // 0.5 * (110 + 110 * 0.2 + 110 * 1)
                    'maximalPrice' => 121,
                    'indexerMinimalPrice' => 132
                ]
            ],

            '
                #4 Testing price for fixed bundle product
                with special price, fixed sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration2(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 20 + 110 * 1)
                    'minimalPrice' => 120,

                    // 0.5 * (110 + 1 * 20 + 110 * 1)
                    'maximalPrice' => 120,
                    'indexerMinimalPrice' => 130
                ]
            ],

            '
                #5 Testing price for fixed bundle product
                with special price, percent sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration2(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 110 * 0.2) + 100
                    'minimalPrice' => 166,

                    // 0.5 * (110 + 110 * 0.2) + 100
                    'maximalPrice' => 166,
                    'indexerMinimalPrice' => 132
                ]
            ],

            '
                #6 Testing price for fixed bundle product
                with special price, fixed sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration3(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * 110 + 100
                    'minimalPrice' => 155,

                    // 0.5 * (110 + 2 * 20) + 100
                    'maximalPrice' => 175,
                    'indexerMinimalPrice' => 150
                ]
            ],

            '
                #7 Testing price for fixed bundle product
                with special price, percent sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration3(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 110 * 1)
                    'minimalPrice' => 110,

                    // 0.5 * (110 + 2 * 110 * 0.2 + 1 * 110)
                    'maximalPrice' => 132,
                    'indexerMinimalPrice' => 154
                ]
            ],

            '
                #8 Testing price for fixed bundle product
                with special price, fixed sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration3(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 110)
                    'minimalPrice' => 110,

                    // 0.5 * (110 + 2 * 20 + 1 * 110)
                    'maximalPrice' => 130,
                    'indexerMinimalPrice' => 150
                ]
            ],

            '
                #9 Testing price for fixed bundle product
                with special price, percent sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration3(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * 110 + 100
                    'minimalPrice' => 155,

                    // 0.5 * (110 + 2 * 0.2 * 110) + 100
                    'maximalPrice' => 177,
                    'indexerMinimalPrice' => 154
                ]
            ],

            '
                #10 Testing price for fixed bundle product
                with special price, fixed sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration4(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 3 * 10) + 100
                    'minimalPrice' => 170,

                    // 0.5 * (110 + 3 * 10 + 1 * 40) + 100
                    'maximalPrice' => 190,
                    'indexerMinimalPrice' => 140
                ]
            ],

            '
                #11 Testing price for fixed bundle product
                with special price, percent sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration4(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 3 * 110 * 0.1 + 110 * 1)
                    'minimalPrice' => 126.5,

                    // 0.5 * (110 + 3 * 110 * 0.1 + 1 * 110 * 0.4 + 110 * 1)
                    'maximalPrice' => 148.5,
                    'indexerMinimalPrice' => 143
                ]
            ],

            '
                #12 Testing price for fixed bundle product
                with special price, fixed sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration4(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 3 * 10 + 1 * 110)
                    'minimalPrice' => 125,

                    // 0.5 * (110 + 3 * 10 + 1 * 40 + 1 * 110)
                    'maximalPrice' => 145,
                    'indexerMinimalPrice' => 140
                ]
            ],

            '
                #13 Testing price for fixed bundle product
                with special price, percent sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration4(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 3 * 110 * 0.1) + 100
                    'minimalPrice' => 171.5,

                    // 0.5 * (110 + 3 * 110 * 0.1 + 1 * 110 * 0.4) + 100
                    'maximalPrice' => 193.5,
                    'indexerMinimalPrice' => 143
                ]
            ],

            '
                #14 Testing price for fixed bundle product
                with special price, fixed sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration5(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 40) + 100
                    'minimalPrice' => 175,

                    // 0.5 * (110 + 1 * 40 + 3 * 15) + 100
                    'maximalPrice' => 197.5,
                    'indexerMinimalPrice' => 150
                ]
            ],

            '
                #15 Testing price for fixed bundle product
                with special price, percent sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration5(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 110 * 0.4 + 1 * 110)
                    'minimalPrice' => 132,

                    // 0.5 * (110 + 1 * 110 * 0.4 + 3 * 110 * 0.15 + 110 * 1)
                    'maximalPrice' => 156.75,
                    'indexerMinimalPrice' => 154
                ]
            ],

            '
                #16 Testing price for fixed bundle product
                with special price, fixed sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration5(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 40 + 1 * 110)
                    'minimalPrice' => 130,

                    // 0.5 * (110 + 1 * 40 + 3 * 15 + 1 * 110)
                    'maximalPrice' => 152.5,
                    'indexerMinimalPrice' => 150
                ]
            ],

            '
                #17 Testing price for fixed bundle product
                with special price, percent sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration5(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 110 * 0.4) + 100
                    'minimalPrice' => 177,

                    // 0.5 * (110 + 1 * 110 * 0.4 + 3 * 110 * 0.15) + 100
                    'maximalPrice' => 201.75,
                    'indexerMinimalPrice' => 154
                ]
            ],

            '
                #18 Testing price for fixed bundle product
                with special price, fixed sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration6(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 40) + 100
                    'minimalPrice' => 175,

                    // 0.5 * (110 + 3 * 15) + 100
                    'maximalPrice' => 177.5,
                    'indexerMinimalPrice' => 150
                ]
            ],

            '
                #19 Testing price for fixed bundle product
                with special price, percent sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration6(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 110 * 0.4 + 1 * 110)
                    'minimalPrice' => 132,

                    // 0.5 * (110 + 3 * 110 * 0.15 + 1 * 110)
                    'maximalPrice' => 134.75,
                    'indexerMinimalPrice' => 154
                ]
            ],

            '
                #20 Testing price for fixed bundle product
                with special price, fixed sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration6(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 40 + 110 * 1)
                    'minimalPrice' => 130,

                    // 0.5 * (110 + 3 * 15 + 110 * 1)
                    'maximalPrice' => 132.5,
                    'indexerMinimalPrice' => 150
                ]
            ],

            '
                #21 Testing price for fixed bundle product
                with special price, percent sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration6(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 110 * 0.4) + 100
                    'minimalPrice' => 177,

                    // 0.5 * (110 + 3 * 110 * 0.15) + 100
                    'maximalPrice' => 179.75,
                    'indexerMinimalPrice' => 154
                ]
            ],

            '
                #22 Testing price for fixed bundle product
                with special price, fixed sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration7(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 40 + 1 * 20) + 100
                    'minimalPrice' => 185,

                    // 0.5 * (110 + 3 * 15 + 1 * 20 + 3 * 10) + 100
                    'maximalPrice' => 202.5,
                    'indexerMinimalPrice' => 170
                ]
            ],

            '
                #23 Testing price for fixed bundle product
                with special price, percent sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration7(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 110 * 0.4 + 1 * 110 * 0.2 + 110 * 1)
                    'minimalPrice' => 143,

                    // 0.5 * (110 + 3 * 110 * 0.15 + 1 * 110 * 0.2 + 3 * 110 * 0.1 + 110 * 1)
                    'maximalPrice' => 162.25,
                    'indexerMinimalPrice' => 176
                ]
            ],

            '
                #24 Testing price for fixed bundle product 
                with special price, fixed sub items and percent options
            ' => [
                'strategy' => $this->getBundleConfiguration7(
                    LinkInterface::PRICE_TYPE_FIXED,
                    self::CUSTOM_OPTION_PRICE_TYPE_PERCENT
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 40 + 1 * 20 + 1 * 110)
                    'minimalPrice' => 140,

                    // 0.5 * (110 + 3 * 15 + 1 * 20 + 3 * 10 + 1 * 110)
                    'maximalPrice' => 157.5,
                    'indexerMinimalPrice' => 170,
                ]
            ],

            '
                #25 Testing price for fixed bundle product 
                with special price, percent sub items and fixed options
            ' => [
                'strategy' => $this->getBundleConfiguration7(
                    LinkInterface::PRICE_TYPE_PERCENT,
                    self::CUSTOM_OPTION_PRICE_TYPE_FIXED
                ),
                'expectedResults' => [
                    // 0.5 * (110 + 1 * 110 * 0.4 + 1 * 110 * 0.2) + 100
                    'minimalPrice' => 188,

                    // 0.5 * (110 + 3 * 110 * 0.15 + 1 * 110 * 0.2 + 3 * 110 * 0.1) + 100
                    'maximalPrice' => 207.25,
                    'indexerMinimalPrice' => 176
                ]
            ],
        ];
    }

    /**
     * Fixed bundle product with special price and without any sub items and options
     * @return array
     */
    private function getBundleConfiguration1()
    {
        return [];
    }

    /**
     * Fixed bundle product with required option, custom option and with special price
     * @param $selectionsPriceType
     * @param $customOptionsPriceType
     * @return array
     */
    private function getBundleConfiguration2(
        $selectionsPriceType,
        $customOptionsPriceType
    ) {
        $optionsData = [
            [
                'title' => 'Op1',
                'required' => true,
                'type' => 'checkbox',
                'links' => [
                    [
                        'sku' => 'simple1',
                        'qty' => 1,
                        'price' => 20,
                        'price_type' => $selectionsPriceType
                    ],
                ]
            ]
        ];

        $customOptionsData = [
            [
                'price_type' => $customOptionsPriceType,
                'title' => 'Test Field',
                'type' => 'field',
                'is_require' => 1,
                'price' => 100,
                'sku' => '1-text',
            ]
        ];

        return [
            [
                'modifierName' => 'addSimpleProduct',
                'data' => [$optionsData]
            ],
            [
                'modifierName' => 'addCustomOption',
                'data' => [$customOptionsData]
            ],
        ];
    }

    /**
     * Fixed bundle product with non required option, custom option and with special price
     * @param $selectionsPriceType
     * @param $customOptionsPriceType
     * @return array
     */
    private function getBundleConfiguration3(
        $selectionsPriceType,
        $customOptionsPriceType
    ) {
        $optionsData = [
            [
                'title' => 'Op1',
                'type' => 'checkbox',
                'required' => false,
                'links' => [
                    [
                        'sku' => 'simple1',
                        'price' => 20,
                        'qty' => 2,
                        'price_type' => $selectionsPriceType
                    ],
                ]
            ]
        ];

        $customOptionsData = [
            [
                'price_type' => $customOptionsPriceType,
                'title' => 'Test Field',
                'type' => 'field',
                'is_require' => 1,
                'price' => 100,
                'sku' => '1-text',
            ]
        ];

        return [
            [
                'modifierName' => 'addSimpleProduct',
                'data' => [$optionsData]
            ],
            [
                'modifierName' => 'addCustomOption',
                'data' => [$customOptionsData]
            ],
        ];
    }

    /**
     * Fixed bundle product with checkbox type option, custom option and with special price
     * @param $selectionsPriceType
     * @param $customOptionsPriceType
     * @return array
     */
    private function getBundleConfiguration4(
        $selectionsPriceType,
        $customOptionsPriceType
    ) {
        $optionsData = [
            [
                'title' => 'Op1',
                'required' => true,
                'type' => 'checkbox',
                'links' => [
                    [
                        'sku' => 'simple1',
                        'qty' => 1,
                        'price' => 40,
                        'price_type' => $selectionsPriceType
                    ],
                    [
                        'sku' => 'simple2',
                        'price' => 10,
                        'qty' => 3,
                        'price_type' => $selectionsPriceType
                    ],
                ]
            ]
        ];

        $customOptionsData = [
            [
                'price_type' => $customOptionsPriceType,
                'title' => 'Test Field',
                'type' => 'field',
                'is_require' => 1,
                'price' => 100,
                'sku' => '1-text',
            ]
        ];

        return [
            [
                'modifierName' => 'addSimpleProduct',
                'data' => [$optionsData]
            ],
            [
                'modifierName' => 'addCustomOption',
                'data' => [$customOptionsData]
            ],
        ];
    }

    /**
     * Fixed bundle product with multi type option, custom option and with special price
     * @param $selectionsPriceType
     * @param $customOptionsPriceType
     * @return array
     */
    private function getBundleConfiguration5(
        $selectionsPriceType,
        $customOptionsPriceType
    ) {
        $optionsData = [
            [
                'title' => 'Op1',
                'required' => true,
                'type' => 'multi',
                'links' => [
                    [
                        'sku' => 'simple1',
                        'qty' => 1,
                        'price' => 40,
                        'price_type' => $selectionsPriceType
                    ],
                    [
                        'sku' => 'simple2',
                        'price' => 15,
                        'qty' => 3,
                        'price_type' => $selectionsPriceType
                    ],
                ]
            ]
        ];

        $customOptionsData = [
            [
                'price_type' => $customOptionsPriceType,
                'title' => 'Test Field',
                'type' => 'field',
                'is_require' => 1,
                'price' => 100,
                'sku' => '1-text',
            ]
        ];

        return [
            [
                'modifierName' => 'addSimpleProduct',
                'data' => [$optionsData]
            ],
            [
                'modifierName' => 'addCustomOption',
                'data' => [$customOptionsData]
            ],
        ];
    }

    /**
     * Fixed bundle product with radio type option, custom option and with special price
     * @param $selectionsPriceType
     * @param $customOptionsPriceType
     * @return array
     */
    private function getBundleConfiguration6(
        $selectionsPriceType,
        $customOptionsPriceType
    ) {
        $optionsData = [
            [
                'title' => 'Op1',
                'required' => true,
                'type' => 'radio',
                'links' => [
                    [
                        'sku' => 'simple1',
                        'qty' => 1,
                        'price' => 40,
                        'price_type' => $selectionsPriceType
                    ],
                    [
                        'sku' => 'simple2',
                        'price' => 15,
                        'qty' => 3,
                        'price_type' => $selectionsPriceType
                    ],
                ]
            ]
        ];

        $customOptionsData = [
            [
                'price_type' => $customOptionsPriceType,
                'title' => 'Test Field',
                'type' => 'field',
                'is_require' => 1,
                'price' => 100,
                'sku' => '1-text',
            ]
        ];

        return [
            [
                'modifierName' => 'addSimpleProduct',
                'data' => [$optionsData]
            ],
            [
                'modifierName' => 'addCustomOption',
                'data' => [$customOptionsData]
            ],
        ];
    }

    /**
     * Fixed bundle product with two required options, custom option and with special price
     * @param $selectionsPriceType
     * @param $customOptionsPriceType
     * @return array
     */
    private function getBundleConfiguration7(
        $selectionsPriceType,
        $customOptionsPriceType
    ) {
        $optionsData = [
            [
                'title' => 'Op1',
                'required' => true,
                'type' => 'radio',
                'links' => [
                    [
                        'sku' => 'simple1',
                        'qty' => 1,
                        'price' => 40,
                        'price_type' => $selectionsPriceType
                    ],
                    [
                        'sku' => 'simple2',
                        'price' => 15,
                        'qty' => 3,
                        'price_type' => $selectionsPriceType
                    ],
                ]
            ],
            [
                'title' => 'Op2',
                'required' => true,
                'type' => 'checkbox',
                'links' => [
                    [
                        'sku' => 'simple1',
                        'qty' => 1,
                        'price' => 20,
                        'price_type' => $selectionsPriceType
                    ],
                    [
                        'sku' => 'simple2',
                        'price' => 10,
                        'qty' => 3,
                        'price_type' => $selectionsPriceType
                    ],
                ]
            ]
        ];

        $customOptionsData = [
            [
                'price_type' => $customOptionsPriceType,
                'title' => 'Test Field',
                'type' => 'field',
                'is_require' => 1,
                'price' => 100,
                'sku' => '1-text',
            ]
        ];

        return [
            [
                'modifierName' => 'addSimpleProduct',
                'data' => [$optionsData]
            ],
            [
                'modifierName' => 'addCustomOption',
                'data' => [$customOptionsData]
            ],
        ];
    }
}
