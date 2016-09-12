<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;
use ArsThanea\KunstmaanExtraBundle\WysiwygFilter\WysiwygFilter;

/**
 * Format HTML content directly from the CMS:
 *
 *  * Add specified classes to given HTML elements
 *  * Strip all non-listed tags
 *
 * For example:
 *
 * '<div><p>Hello <b>World!</b></p></div>'|bem({
 *   'p': "landingPageAnswer__text",
 *   'b': null,
 * })
 *
 * Becomes: <p class="landingPageAnswer__text">Hello <b>World!</b></p>
 *
 */
class BemTwigExtension extends \Twig_Extension
{
    /**
     * @var WysiwygFilter
     */
    private $wysiwygFilter;

    /**
     * @param WysiwygFilter $wysiwygFilter
     */
    public function __construct(WysiwygFilter $wysiwygFilter)
    {
        $this->wysiwygFilter = $wysiwygFilter;
    }

    public function getFilters()
    {
        return [
            'bem' => new \Twig_SimpleFilter('bem', [$this, 'functionBem'], ['is_safe' => ['html' => true]])
        ];
    }

    public function functionBem($html, $allowed = null)
    {
        return $this->wysiwygFilter->filter($html, $allowed);

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bem_twig';
    }
}
