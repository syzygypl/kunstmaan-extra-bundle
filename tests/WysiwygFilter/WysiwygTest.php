<?php

namespace ArsThanea\KunstmaanExtraBundle\WysiwygFilter;

class ElementConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param $configuration
     * @param $expected
     * @param array $examples
     *
     * @dataProvider samples
     */
    public function testParsing($configuration, $expected, array $examples = [])
    {
        $parser = new ElementConfiguration();

        $result = $parser->parse($configuration);

        $this->assertSame($expected, $result);

        $filter = new WysiwygFilter();

        foreach ($examples as $html => $sanitized) {
            $this->assertSame($sanitized, $filter->filter($html, $configuration));
        }
    }

    public function samples()
    {
        return [
            'simple elements are treated as keys' =>
                [['p'], ['p' => []], [
                    'Text' => '<p>Text</p>',
                    '<p>Text' => '<p>Text</p>',
                    '<p class="foobar">Text' => '<p>Text</p>',
                ]],
            'default attribute is class' =>
                [['p' => 'bem__sample'], ['p' => ['class' => 'bem__sample']], [
                    '<p>' => '<p class="bem__sample"></p>',
                    '<p class="p">' => '<p class="bem__sample"></p>',
                    '<p foobar="value">' => '<p class="bem__sample"></p>',
                ]],

            'list of allowed attributes' =>
                [['p' => ['class', 'id']], ['p' => ['class' => null, 'id' => null]], [
                    '<p class="foo" id="bar">' => '<p class="foo" id="bar"></p>'
                ]],

            'list of allowed attributes with forced values' =>
                [['p' => ['class' => 'bem__sample', 'id']], ['p' => ['class' => 'bem__sample', 'id' => null]], [
                    '<p class="foo" id="bar">' => '<p class="bem__sample" id="bar"></p>'
                ]],

            'dot notation' =>
                [['a.href.id.target'], ['a' => ['href' => null, 'id' => null, 'target' => null]], [
                    '<a href="#foo" id="bar" target="_blank" hreflang="en">' => '<a href="#foo" id="bar" target="_blank"></a>'
                ]],

            'dot notation with class' =>
                [['a.href.id.target' => 'bem__sample'], ['a' => ['href' => null, 'id' => null, 'target' => null, 'class' => 'bem__sample']]],

            'using null (for yaml)' =>
                [['a.foo' => null], ['a' => ['foo' => null]]],

            'nested tags' =>
                [['p' => 'bem__sample', 'strong' => null], ['p' => ['class' => 'bem__sample'], 'strong' => []], [
                    '<p>Sample <strong>text' => '<p class="bem__sample">Sample <strong>text</strong></p>'
                ]],
        ];
    }
}
