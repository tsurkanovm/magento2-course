<?php

namespace Training\Test\Controller;

use Magento\Catalog\Controller\Product\View;

class ProductViewOverride extends View
{
    public function execute() {
        echo "ONE"; exit;
    }
}
