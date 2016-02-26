<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\Category;
use ArsThanea\KunstmaanExtraBundle\Url\PageUrlService;
use Elastica\Result;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PageUrlTwigExtension extends \Twig_Extension
{
    /**
     * @var PageUrlService
     */
    private $pageUrlService;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    public function __construct(PageUrlService $pageUrlService, UrlGeneratorInterface $generator)
    {
        $this->pageUrlService = $pageUrlService;
        $this->generator = $generator;
    }

    public function getFunctions()
    {
        return [
            'page_url' => new \Twig_SimpleFunction('page_url', [$this, 'getPageUrl']),
        ];
    }

    public function getPageUrl($url, $absolute = false, array $parameters = [])
    {
        if ($url instanceof HasNodeInterface) {
            return $this->pageUrlService->generate($url, $absolute, $parameters);
        }

        if ($url instanceof NodeTranslation) {
            $url = $url->getUrl();
        } elseif ($url instanceof Category) {
            $url = $url->getSlug();
        } elseif ($url instanceof Result) {
            $url = $url->__get('slug');
        } elseif (is_object($url) && method_exists($url, '__toString')) {
            $url = (string)$url;
        } elseif (is_string($url)) {
            // noop
        } else {
            throw new \InvalidArgumentException(sprintf('Cannot convert %s to URL string',
                is_object($url) ? get_class($url) : getType($url)));
        }

        return $this->generator->generate(
            "_slug",
            ["url" => ltrim($url, '/')] + $parameters,
            $absolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kunstmaan_extra_page_url';
    }
}
