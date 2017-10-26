<?php

namespace Unit1\SecondModule\Plugin\Block;

use Magento\Framework\Phrase;
use Magento\Theme\Block\Html\Breadcrumbs as ThemeBreadcrumbs;

class Breadcrumbs
{
    public function beforeAddCrumb(ThemeBreadcrumbs $subject, string $crumbName, array $crumbInfo)
    {
        if ($oldLabel = $crumbInfo['label'] ?? false) {
            if ($oldLabel instanceof Phrase) {
                $newLabel = new Phrase($oldLabel->getText() . '(!)', $oldLabel->getArguments());
                $newLabel->setRenderer($oldLabel->getRenderer());
                $crumbInfo['label'] = $newLabel;
            } elseif (is_string($oldLabel) ){
                $crumbInfo['label'] = $oldLabel . '(!)';
            }
        }

        return [$crumbName, $crumbInfo];
    }
}
