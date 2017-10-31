<?php
namespace Unit3\JS\Block;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Template;

class ActionNameGetter extends Template
{
    /**
     * @var Http
     */
    private $request;

    /**
     * @inheritdoc
     */
    public function __construct(Template\Context $context, array $data = [])
    {
        $this->request = $context->getRequest();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->request->getFullActionName();
    }
}
