<?php

namespace Unit1\FirstModule\MagentoU;

class Test
{
    public $productRepository;
    public $productFactory;
    public $session;
    public $unit1ProductRepository;
    public $justAParameter;
    public $data;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Checkout\Model\Session $session,
        \Unit1\FirstModule\Api\ProductRepositoryInterface $unit1ProductRepository,
        $justAParameter = '',
        array $data
    )
    {
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->session = $session;
        $this->unit1ProductRepository = $unit1ProductRepository;
        $this->justAParameter = $justAParameter;
        $this->data = $data;
    }
}
