<?php

namespace Unit3\Test\Controller\Block;

use Magento\Framework\App\Action\Action;
use Magento\Backend\Block\Template as BlockTemplate;

/**
 * Class Template
 * @package Unit3\Test\Controller\Block\Template
 */
class CheckMyTemplate extends Action
{
    public function execute()
    {
        $layout = $this->_view->getLayout();
        $block = $layout->createBlock('Magento\Framework\View\Element\Template');
        /** @var BlockTemplate $block */
        $block->setTemplate('Unit3_Test::test.phtml');
        $this->getResponse()->appendBody($block->toHtml());
    }
}
