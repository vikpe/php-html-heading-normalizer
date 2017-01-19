<?php

namespace Vikpe;

class HtmlHeadingNormalizerTest extends \PHPUnit_Framework_TestCase
{
    private function getTestFileContents($filename)
    {
        return file_get_contents(__DIR__.'/file/'.$filename);
    }

    public static function stripWhitespace($string)
    {
        $needlePattern = ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/> </s'];
        $replacements = ['>', '<', '\\1', '><'];

        return preg_replace($needlePattern, $replacements, $string);
    }

    public function assertHtmlStringEqualsHtmlString($expect, $actual)
    {
        $this->assertEquals(self::stripWhitespace($expect), self::stripWhitespace($actual));
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
            array('<h1>foo</h1>', 0, '<h1>foo</h1>'),
        );
    }

    public function testDemoteHtmlDocument()
    {
        $inputHtml = $this->getTestFileContents('document.base1.html');
        $demotedHtml = HtmlHeadingNormalizer::demote($inputHtml, 2);
        $expectedHtml = $this->getTestFileContents('document.base3.html');

        $this->assertHtmlStringEqualsHtmlString($expectedHtml, $demotedHtml);
    }

    public function testDemoteHtmlString()
    {
        $inputHtml = $this->getTestFileContents('html.string.base1.html');
        $demotedHtml = HtmlHeadingNormalizer::demote($inputHtml, 2);
        $expectedHtml = $this->getTestFileContents('html.string.base3.html');

        $this->assertHtmlStringEqualsHtmlString($expectedHtml, $demotedHtml);
    }

    /**
     * @dataProvider promoteSimpleHtmlStringsDataProvider
     */
    public function testPromoteSimpleHtmlStrings($html, $numberOfLevels, $expect)
    {
        $actual = HtmlHeadingNormalizer::promote($html, $numberOfLevels);

        $this->assertEquals($expect, $actual);
    }

    public function promoteSimpleHtmlStringsDataProvider()
    {
        return array(
            array('', 1, ''),
            array('<p>foo</p>', 1, '<p>foo</p>'),
            array('<h1>foo</h1>', 0, '<h1>foo</h1>'),
        );
    }

    public function testPromoteHtmlDocument()
    {
        $inputHtml = $this->getTestFileContents('document.base3.html');
        $promotedHtml = HtmlHeadingNormalizer::promote($inputHtml, 2);
        $expectedHtml = $this->getTestFileContents('document.base1.html');

        $this->assertHtmlStringEqualsHtmlString($expectedHtml, $promotedHtml);
    }

    public function testPromoteHtmlString()
    {
        $inputHtml = $this->getTestFileContents('html.string.base3.html');
        $promotedHtml = HtmlHeadingNormalizer::promote($inputHtml, 2);
        $expectedHtml = $this->getTestFileContents('html.string.base1.html');

        $this->assertHtmlStringEqualsHtmlString($expectedHtml, $promotedHtml);
    }
}
