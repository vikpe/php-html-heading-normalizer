<?php

namespace Vikpe;

class HtmlHeadingNormalizerTest extends \PHPUnit_Framework_TestCase
{
    private function getTestFileContents($filename)
    {
        return file_get_contents(__DIR__.'/file/'.$filename);
    }

    public function assertHtmlStringEqualsHtmlString($expect, $actual)
    {
        $expectedDom = new \DOMDocument();
        $expectedDom->loadHtml($expect);
        $expectedDom->preserveWhiteSpace = false;

        $actualDom = new \DOMDocument();
        $actualDom->loadHTML($actual);
        $actualDom->preserveWhiteSpace = false;

        $this->assertEquals(
            $expectedDom->saveHTML(),
            $actualDom->saveHTML()
        );
    }

    /**
     * @dataProvider normalizeDataProvider
     */
    public function testNormalizeSimpleHtmlStrings($html, $expect)
    {
        $actual = HtmlHeadingNormalizer::normalize($html);

        $this->assertEquals($expect, $actual);
    }

    public function normalizeDataProvider()
    {
        return array(
            array('', ''),
            array('<p>foo</p>', '<p>foo</p>'),
        );
    }

    public function testNormalizePromoteHtmlDocument()
    {
        $inputHtml = $this->getTestFileContents('document.base1.html');
        $normalizedHtml = HtmlHeadingNormalizer::normalize($inputHtml, 3);

        $expectedHtml = $this->getTestFileContents('document.base1.promote3.html');

        $this->assertHtmlStringEqualsHtmlString($expectedHtml, $normalizedHtml);
    }
}
