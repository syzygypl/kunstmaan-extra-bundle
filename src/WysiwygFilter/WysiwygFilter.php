<?php

namespace ArsThanea\KunstmaanExtraBundle\WysiwygFilter;

use Symfony\Component\DomCrawler\Crawler;

class WysiwygFilter
{
    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @var ElementConfiguration
     */
    private $parser;

    private $allowed = [];

    /**
     * @param Crawler $crawler
     * @param array   $configuration
     */
    public function __construct(Crawler $crawler = null, array $configuration = [])
    {
        $this->crawler = $crawler ?: new Crawler();
        $this->parser = new ElementConfiguration();
        $this->allowed = array_map(function ($value) {
            return $this->parser->parse($value);
        }, $configuration);
    }


    public function filter($html, $allowed = null)
    {
        $mapping = $this->getMapping($allowed);

        $html = $this->normalize($html);

        $this->crawler->clear();
        $this->crawler->addHtmlContent(sprintf('<div>%s</div>', $html));

        // first, remove all blacklisted nodes incl. their contain (strip tags would leave the content):
        $this->crawler->filter('style, script')->each(function (Crawler $blacklisted) {
            /** @var \DOMElement $node */
            foreach ($blacklisted as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        $this->crawler->filter(implode(", ", array_keys($mapping)))->each(function (Crawler $element) use ($mapping) {
            $nodeName = $element->nodeName();
            if (false === isset($mapping[$nodeName])) {
                return ;
            };

            $config = $mapping[$nodeName];
            $DOMElement = $element->getNode(0);

            // warning: attributes is a map that reindexes when removing an attribute
            // you need to copy it for iteraton
            foreach (iterator_to_array($DOMElement->attributes) as $attr) {
                if (false === array_key_exists($attr->nodeName, $config)) {
                    $DOMElement->removeAttributeNode($attr);
                }
            }

            foreach ($config as $attrName => $value) {
                if (null !== $value) {
                    $element->getNode(0)->setAttribute($attrName, $value);
                }
            }
        });

        return strip_tags($this->crawler->html(), array_reduce(array_keys($mapping), function ($allowed, $tag) {
            return $allowed . sprintf('<%s>', $tag);
        }, ""));
    }

    private function getMapping($allowed)
    {
        if (is_array($allowed)) {
            return $this->parser->parse($allowed);
        }

        if (is_string($allowed)) {
            if (false === isset($this->allowed[$allowed])) {
                throw new \InvalidArgumentException('Invalid bem mapping: ' . $allowed);
            }

            return $this->allowed[$allowed];
        }

        return reset($this->allowed);
    }

    private function normalize($html)
    {
        if (0 === strpos($html, '<')) {
            return $html;
        }

        return implode("", array_map(function ($content) {
            return sprintf('<p>%s</p>', $content);
        }, preg_split("/(\r?\n){2,}/", $html)));
    }
}
