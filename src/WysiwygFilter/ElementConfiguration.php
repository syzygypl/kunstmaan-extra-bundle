<?php

namespace ArsThanea\KunstmaanExtraBundle\WysiwygFilter;

class ElementConfiguration
{
    public function parse(array $elements)
    {
        return iterator_to_array($this->parseElements($elements));
    }

    public function parseElements(array $elements)
    {
        foreach ($elements as $name => $values) {
            if (is_string($values) && is_numeric($name)) {
                $name = $values;
                $values = [];
            }


            if (is_string($values)) {
                $values = ['class' => $values];
            } elseif (null === $values) {
                $values = [];
            }

            if (false !== strpos($name, '.')) {
                $values = array_merge(explode('.', $name), $values);
                $name = array_shift($values);
            }

            yield strtolower($name) => iterator_to_array($this->parseValues((array)$values));
        }
    }

    private function parseValues(array $items)
    {
        foreach ($items as $name => $value) {
            if (is_string($value) && is_numeric($name)) {
                $name = $value;
                $value = null;
            }

            yield strtolower($name) => $value;
        }
    }
}
