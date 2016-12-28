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
     * @dataProvider demoteSimpleHtmlStringsDataProvider
     */
    public function testDemoteSimpleHtmlStrings($html, $numberOfLevels, $expect)
    {
        $actual = HtmlHeadingNormalizer::demote($html, $numberOfLevels);

        $this->assertEquals($expect, $actual);
    }

    public function demoteSimpleHtmlStringsDataProvider()
    {
        return array(
            array('', 1, ''),
            array('<p>foo</p>', 1, '<p>foo</p>'),
        );
    }

    public function testDemoteHtmlDocument()
    {
        $inputHtml = $this->getTestFileContents('document.base1.html');
        $normalizedHtml = HtmlHeadingNormalizer::demote($inputHtml, 2);

        $expectedHtml = $this->getTestFileContents('document.base1.demote2.html');

        $this->assertHtmlStringEqualsHtmlString($expectedHtml, $normalizedHtml);
    }
}
