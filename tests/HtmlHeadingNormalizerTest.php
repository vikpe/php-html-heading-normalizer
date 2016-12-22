<?php

namespace Vikpe;

class HtmlHeadingNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $this->assertEquals(
            '',
            HtmlHeadingNormalizer::normalize('')
        );

        /////

        $html = '<h2>Foo</h2><p>bar</p>';

        $expect = '<h2>Foo</h2><p>bar</p>';
        $actual = HtmlHeadingNormalizer::normalize($html);

        $this->assertEquals($expect, $actual);
    }
}
