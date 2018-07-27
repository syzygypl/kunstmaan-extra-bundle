<?php

namespace {{ namespace }}\Form;

use Symfony\Component\Form\AbstractType
use Symfony\Component\Form\FormBuilderInterface;

/**
 * The type for {{ entity_class }}
 */
class {{ entity_class }}AdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
{% for field in fields %}
        $builder->add('{{ field }}');
{% endfor %}
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return '{{ entity_class|lower }}_form';
    }
}
