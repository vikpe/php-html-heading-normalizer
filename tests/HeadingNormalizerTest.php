<?php

use Vikpe\HeadingNormalizer;

class HeadingNormalizerTestCase extends PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $html = '
        <h2>Foo</h2>
        <p>bar</h2>
        ';

        $expect = '
        <h3>Foo</h3>
        <p>bar</h2>
        ';

        $actual = HeadingNormalizer::normalize($html, 3);

        $this->assertEquals($expect, $actual);
    }
}
