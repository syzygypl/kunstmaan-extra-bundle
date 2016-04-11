<?php

namespace ArsThanea\KunstmaanExtraBundle\Form\Extension\DataTransformer;

use ArsThanea\KunstmaanExtraBundle\SiteTree\CurrentLocaleInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class NodeTranslationToUrlTransformer implements DataTransformerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }


    /**
     * @param NodeTranslation $value
     *
     * @return string
     */
    public function transform($value)
    {
        if (null === $value) {
            return "";
        }

        if (false === $value instanceof NodeTranslation) {
            throw new TransformationFailedException("Value must be instance of NodeTranslation");
        }

        return $this->router->generate('_slug', [
            '_locale' => $value->getLang(),
            'url' => $value->getUrl(),
        ]);
    }

    /**
     * @param string $value
     *
     * @return NodeTranslation|null
     */
    public function reverseTransform($value)
    {
        if ("" === $value || null === $value) {
            return null;
        }

        try {
            $route = $this->router->match($value);

            if (false === isset($route['_nodeTranslation']) || false === $route['_nodeTranslation'] instanceof NodeTranslation) {
                throw new TransformationFailedException('Matched route has no nodeTranslation');
            }

            return $route['_nodeTranslation'];

        } catch (RouteNotFoundException $e) {
            throw new TransformationFailedException('Cannot match URL to a nodeTranslation', 0, $e);
        }
    }
}
