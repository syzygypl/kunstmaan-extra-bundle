<?php

namespace ArsThanea\KunstmaanExtraBundle\Typography;

use ArsThanea\KunstmaanExtraBundle\Twig\TypographyTwigExtension;

class TypographyTwigExtensionTest extends \PHPUnit_Framework_TestCase
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
        return [
            ['this is an orphan', 'this is an orphan'],
            ['<a href="http://foo.bar">baz a bar</a>', '<a href="http://foo.bar">baz a bar</a>'],
            ['<a title="this is an orphan">an orphan</a>', '<a title="this is an orphan">an orphan</a>', ],
            ['<span>this is</span><a> an orphan', '<span>this is</span><a> an orphan'],
            ['<span>this is</span></a> an orphan', '<span>this is</span></a> an orphan'],
            ['is <a href="foo">bar</a>', 'is <a href="foo">bar</a>'],
            ['<span>this is</span></a> an orphan <span>other a</span> orphan', '<span>this is</span></a> an orphan <span>other a</span> orphan'],
            ['this is  an  orphan', 'this is an orphan'],
            ['10 000', '10 000'],
            ['foo an bar', 'foo an bar'],
        ];
    }
}
