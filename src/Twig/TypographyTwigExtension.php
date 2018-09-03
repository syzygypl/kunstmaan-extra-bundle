<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

class TypographyTwigExtension extends \Twig_Extension
{
    private $regex;

    public function __construct($regex = null)
    {
        $this->regex = $regex ?: '/
            \b            # start at a word boundary
            (?<!\<)       # exclude opening HTML tags
            (             # first matching group is important for replace
                \w{1,2}\.?      # orphan optionally followed by a period
                (?:
                    <\/?\w+>    # don’t let a closing HTML tag brake the match
                    (?!\s*<\w)  # but exclude spaces between HTML tags
                )*?
            )
            \s+           # match one or many whitespace characters
        /umx';
    }


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

        return preg_replace($this->regex, '$1' . $nonBreakingSpace, $text);
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
