<?php

namespace Unit3\Test\Plugin\Block;

use Magento\Catalog\Block\Product\View\Description;

class SetProductDescriptionPlugin
{
    public function beforeToHtml(Description $originalBlock) {
        $originalBlock->getProduct()->setDescription('New!! Test description');
    }
}
