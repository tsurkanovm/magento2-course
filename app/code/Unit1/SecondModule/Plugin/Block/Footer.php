<?php

namespace Unit1\SecondModule\Plugin\Block;

use Magento\Theme\Block\Html\Footer as ThemeFooter;

class Footer
{
    public function afterGetCopyright(ThemeFooter $subject, string $copyright)
    {
        $copyright = 'Customized copyright!';

        return $copyright;
    }
}
