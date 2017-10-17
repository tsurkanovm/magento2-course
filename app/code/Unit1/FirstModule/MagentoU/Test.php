<?php

namespace Unit1\FirstModule\MagentoU;

class Test
{
    public $productRepository;
    public $productFactory;
    public $session;
    public $unit1Product;
    public $justAParameter;
    public $data;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Checkout\Model\Session $session,
        \Unit1\FirstModule\Api\ProductInterfaceFactory $unit1Product,
        $justAParameter = '',
        array $data
    )
    {
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->session = $session;
        $this->unit1Product = $unit1Product;
        $this->justAParameter = $justAParameter;
        $this->data = $data;
    }
}
