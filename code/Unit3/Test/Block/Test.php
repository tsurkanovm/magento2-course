<?php

namespace Unit3\Test\Block;

use Magento\Framework\View\Element\AbstractBlock;

class Test extends AbstractBlock
{
    protected function _toHtml() {
        return "<b>Hello world from block!</b>";
    }
}
