<?php

namespace ArsThanea\KunstmaanExtraBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvancedSelectFormExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['advanced_select']) {
            $view->vars['attr']['class'] =
                (isset($view->vars['attr']['class']) ? $view->vars['attr']['class'] . ' ': '')
                . 'js-advanced-select advanced-select form-control';
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'advanced_select' => false,
        ]);
    }


    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return ChoiceType::class;
    }
}
