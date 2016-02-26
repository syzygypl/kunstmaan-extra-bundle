<?php

namespace ArsThanea\KunstmaanExtraBundle\Form;

use ArsThanea\KunstmaanExtraBundle\Form\Extension\DataTransformer\NodeTranslationToUrlTransformer;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PageUrlType extends AbstractType
{
    /**
     * @var NodeTranslationToUrlTransformer
     */
    private $transformer;

    public function __construct(NodeTranslationToUrlTransformer $transformer)
    {
        $this->transformer = $transformer;
    }


    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }


    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return URLChooserType::class;
    }

}
