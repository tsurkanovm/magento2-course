<?php

namespace Unit3\Test\Controller\Block;

use Magento\Framework\App\Action\Action;

/**
 * Class Index
 * @package Unit3\Test\Controller\Block\Index
 */
class Index extends Action
{
    /**
     * Function execute
     */
    public function execute()
    {
        $layout = $this->_view->getLayout();
        $block = $layout->createBlock('Unit3\Test\Block\Test');
        $this->getResponse()->appendBody($block->toHtml());
    }
}
