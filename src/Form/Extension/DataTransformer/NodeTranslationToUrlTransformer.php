<?php

namespace ArsThanea\KunstmaanExtraBundle\Form\Extension\DataTransformer;

use ArsThanea\KunstmaanExtraBundle\SiteTree\CurrentLocaleInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class NodeTranslationToUrlTransformer implements DataTransformerInterface
{
    /**
     * @var NodeTranslationRepository
     */
    private $repository;
    /**
     * @var CurrentLocaleInterface
     */
    private $currentLocale;

    public function __construct(NodeTranslationRepository $repository, CurrentLocaleInterface $currentLocale)
    {
        $this->repository = $repository;
        $this->currentLocale = $currentLocale;
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

        return '/' . $value->getUrl();
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

        $locale = $this->currentLocale->getCurrentLocale();

        $nodeTranslation = $this->repository->getNodeTranslationForUrl(ltrim($value, '/'), $locale);

        if (null === $nodeTranslation) {
            throw new TransformationFailedException('Cannot find nodeTranslation for given URL');
        }

        return $nodeTranslation;
    }
}
