<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Usage:
 *
 * {% set form = form|form_attributes({
 *   "child.birth_date": "-additional-modifier",
 *   "profile.email": {
 *       "title": "Hello!",
 *       "class": "zzz"
 *   }
 * }) %}
 */
class FormAttributesTwigExtension extends \Twig_Extension
{
    /**
     * @var PropertyAccessor
     */
    private $accessor;

    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }


    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('form_attributes', [$this, 'formAttributes']),
        ];
    }

    /**
     * @param FormView $formView
     * @param array    $data
     *
     * @return FormView
     */
    public function formAttributes(FormView $formView, array $data)
    {
        $formView = clone $formView;

        foreach ($data as $key => $value) {
            $path = 'children[' . str_replace('.', '].children[', $key) . ']';
            if (false === $this->accessor->isReadable($formView, $path)) {
                continue;
            }

            /** @var FormView $field */
            $field = $this->accessor->getValue($formView, $path);

            if (false === $field instanceof FormView) {
                throw new \RuntimeException("Cannot set form attribute: $key is not a FormView instance");
            }

            if (is_string($value)) {
                $value = ["class" => " $value"];
            }

            if (false === isset($field->vars['attr'])) {
                $field->vars['attr'] = [];
            }

            foreach ($value as $name => $attribute) {
                if (isset($field->vars['attr'][$name]) && " " === substr($attribute, 0, 1)) {
                    $attribute = $field->vars['attr'][$name] . $attribute;
                }

                $field->vars['attr'][$name] = trim($attribute);
            }
        }

        return $formView;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form_attributes_twig';
    }
}
