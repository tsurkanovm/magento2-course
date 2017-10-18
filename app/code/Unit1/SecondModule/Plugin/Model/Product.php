<?php

namespace Unit1\SecondModule\Plugin\Model;

use \Magento\Catalog\Model\Product as CatalogProduct;

class Product
{
  public function afterGetPrice(CatalogProduct $product, float $price)
  {
      return $price * 1.5;
  }
}
