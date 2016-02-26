<?php

namespace ArsThanea\KunstmaanExtraBundle\Form\Extension\DataTransformer;

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

    public function __construct(NodeTranslationRepository $repository)
    {
        $this->repository = $repository;
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

        $nodeTranslation = $this->repository->getNodeTranslationForUrl(ltrim($value, '/'));

        if (null === $nodeTranslation) {
            throw new TransformationFailedException('Cannot find nodeTranslation for given URL');
        }

        return $nodeTranslation;
    }
}
