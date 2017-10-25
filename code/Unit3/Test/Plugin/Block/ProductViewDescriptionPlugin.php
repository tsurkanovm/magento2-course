<?php

namespace Unit3\Test\Plugin\Block;

use Magento\Catalog\Block\Product\View\Description;

class ProductViewDescriptionPlugin
{
    public function beforeToHtml(Description $originalBlock) {
        $originalBlock->getProduct()->setDescription('Test description');
    }
}
