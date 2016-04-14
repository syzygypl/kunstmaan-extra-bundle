<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

class TypographyTwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('orphans', [$this, 'orphans']),
        ];
    }

    /**
     * @param $text
     *
     * @return mixed
     */
    public function orphans($text)
    {
        $nonBreakingSpace = ' ';

        return preg_replace("/\\b(\\w{1,2}\\.?) /um", '$1' . $nonBreakingSpace, $text);
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kunstmaan_extra_typography';
    }
}
