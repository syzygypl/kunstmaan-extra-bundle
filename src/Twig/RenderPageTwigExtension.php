<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use ArsThanea\KunstmaanExtraBundle\SiteTree\RefIdProvider\RefIdProviderInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class RenderPageTwigExtension extends \Twig_Extension
{
    /**
     * @var FragmentHandler
     */
    private $fragmentHandler;

    /**
     * @var RefIdProviderInterface
     */
    private $provider;

    public function __construct(FragmentHandler $fragmentHandler, RefIdProviderInterface $provider)
    {
        $this->fragmentHandler = $fragmentHandler;
        $this->provider = $provider;
    }

    public function getFunctions()
    {
        return [
            'esi' => new \Twig_SimpleFunction('esi', [$this, 'esi'], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters()
    {
        return [
            'ref_id' => new \Twig_SimpleFilter('ref_id', [$this, 'getRefId'])
        ];
    }

    public function esi(ControllerReference $controller)
    {
        $controller->attributes = array_map(function ($value) {
            return $this->getRefId($value, true);
        }, $controller->attributes);

        return $this->fragmentHandler->render($controller, 'esi', [
            'standalone' => true,
        ]);
    }

    public function getRefId($node, $default = false)
    {
        $ref = $this->provider->getRefId($node);

        if ($ref) {
            return $ref->getRefId();
        }

        if (false === $default) {
            throw new \InvalidArgumentException('I cant get `refId` from ' . (is_object($node) ? get_class($node) : gettype($node)));
        }

        return $node;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kunstmaan_extra_render_page';
    }
}
