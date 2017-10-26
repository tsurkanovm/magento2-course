<?php

namespace Unit3\Test2\Plugin\Block;

use Magento\Catalog\Block\Product\View\Description;

class ProductTemplateDescriptionPlugin
{
    public function beforeToHtml(Description $originalBlock) {
        $originalBlock->setTemplate('Unit3_Test2::description.phtml');
    }
}
