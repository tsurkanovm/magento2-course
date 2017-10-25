<?php

namespace Unit3\Test\Controller\Block;

use Magento\Framework\App\Action\Action;

/**
 * Class Index
 * @package Unit3\Test\Controller\Block\Index
 */
class Index extends Action
{

    public function execute()
    {
        $layout = $this->_view->getLayout();
        $block = $layout->createBlock('Magento\Framework\View\Element\Text');
        $block->setText("Hello world from text block !");
        $this->getResponse()->appendBody($block->toHtml());
    }
}
