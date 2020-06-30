<?php

namespace Typography;

use ArsThanea\KunstmaanExtraBundle\Twig\TypographyTwigExtension;
use PHPUnit\Framework\TestCase;

class TypographyTwigExtensionTest extends TestCase
{
    /**
     * @param string $input
     * @param string $expected
     * @dataProvider data
     */
    public function testOrphans($input, $expected)
    {
        $service = new TypographyTwigExtension();

        $result = $service->orphans($input);

        $this->assertEquals($expected, $result);
    }

    public function data()
    {
        $nbsp = ' ';
        return [
            'simple test case' =>
                ['this is an orphan', 'this is'.$nbsp.'an'.$nbsp.'orphan'],
            'text inside elements, but not between attributes' =>
                ['<a href="http://foo.bar">baz a bar</a>', '<a href="http://foo.bar">baz a'.$nbsp.'bar</a>'],
            'inside attributes is ok' =>
                ['<a title="this is an orphan">an orphan</a>', '<a title="this is'.$nbsp.'an'.$nbsp.'orphan">an'.$nbsp.'orphan</a>', ],
            'after closing html tag is ok' =>
                ['<span>this is</span></a> an orphan', '<span>this is</span></a>'.$nbsp.'an'.$nbsp.'orphan'],
            'not between two different html tags, space inside' =>
                ['<span>this is</span><a> an orphan', '<span>this is</span><a> an'.$nbsp.'orphan'],
            'not between two different html tags, space between' =>
                ['<span>this is</span></a> <p>an orphan</p>', '<span>this is</span></a> <p>an'.$nbsp.'orphan</p>'],
            'before a HTML element' =>
                ['is <a href="http://example.com">bar</a>', 'is'.$nbsp.'<a href="http://example.com">bar</a>'],
            'many different cases' =>
                ['<span>this is</span></a> an orphan <span>other a</span> orphan', '<span>this is</span></a>'.$nbsp.'an'.$nbsp.'orphan <span>other a</span>'.$nbsp.'orphan'],

            'multiple spaces' =>
                ['this is  an  orphan', 'this is'.$nbsp.'an'.$nbsp.'orphan'],
            'numbers' =>
                ['10 000', '10'.$nbsp.'000'],
            'pre-existing nb spaces' =>
                ['foo an'.$nbsp.'bar', 'foo an'.$nbsp.'bar'],
        ];
    }
}
