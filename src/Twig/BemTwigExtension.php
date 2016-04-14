<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

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
    public function getFilters()
    {
        return [
            'bem' => new \Twig_SimpleFilter('bem', [$this, 'functionBem'], ['is_safe' => ['html' => true]])
        ];
    }

    public function functionBem($string, $mapping = [])
    {
        if (false === is_array($mapping)) {
            $mapping = ['p' => $mapping];
        }

        $keys = array_map(function ($tagName) {
            return sprintf('<%s>', $tagName);
        }, array_keys($mapping));

        $string = strip_tags($string, implode('', $keys));

        $mapping = array_map(function ($tagName, $className) {
            if (!$className) {
                return sprintf('<%s>', $tagName);
            }

            return sprintf('<%s class="%s">', $tagName, $className);
        }, array_keys($mapping), array_values($mapping));

        return strtr($string, array_combine($keys, $mapping));

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bem_twig';
    }
}
