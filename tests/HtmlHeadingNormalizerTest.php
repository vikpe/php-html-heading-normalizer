<?php

namespace Vikpe;

class HtmlHeadingNormalizerTestCase extends \PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $html = '
        <h2>Foo</h2>
        <p>bar</h2>
        ';

        $expect = '
        <h2>Foo</h2>
        <p>bar</h2>
        ';

        $actual = HtmlHeadingNormalizer::normalize($html, 3);

        $this->assertEquals($expect, $actual);
    }
}
