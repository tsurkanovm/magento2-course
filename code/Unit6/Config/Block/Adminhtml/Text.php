<?php

namespace Unit6\Config\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Text extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        return '<span>Hello World!</span>';
    }
}
