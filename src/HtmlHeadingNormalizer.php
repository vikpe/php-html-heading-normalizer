<?php

namespace Vikpe;

class HtmlHeadingNormalizer
{
    public static function normalize($html, $baseLevel = 1)
    {
        if (!self::containsHeadings($html)) {
            return $html;
        }

        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($html);

        $originalHeadings = self::getHeadings($domDocument);
        $normalizedHeadings = self::normalizeHeadings($originalHeadings, $baseLevel);

        foreach ($originalHeadings as $i => $needle) {
            $needle->parentNode->replaceChild($normalizedHeadings[$i], $needle);
        }

        return $domDocument->saveHTML();
    }

    private static function getHeadings(\DOMDocument $domDocument)
    {
        $tagNames = ['h1', 'h2', 'h3', 'h4', 'h6'];

        $headings = [];

        foreach ($tagNames as $tagName) {
            foreach ($domDocument->getElementsByTagName($tagName) as $heading) {
                $headings[] = $heading;
            }
        }

        return $headings;
    }

    private static function normalizeHeadings(array $originalHeadings, $baseLevel)
    {
        $normalizedHeadings = [];

        foreach ($originalHeadings as $heading) {
            $currentLevel = self::tagNameToLevel($heading->tagName);
            $newTagName = self::levelToTagName($baseLevel + $currentLevel - 1);

            $normalizedHeadings[] = self::cloneHeading($heading, $newTagName);
        }

        return $normalizedHeadings;
    }

    private static function containsHeadings($html)
    {
        $heading_needle = '<h';

        return (false !== stripos($html, $heading_needle));
    }

    private static function tagNameToLevel($tagName)
    {
        return substr($tagName, 1);
    }

    private static function levelToTagName($number)
    {
        return 'h'.$number;
    }

    private static function cloneHeading(\DOMElement $sourceHeading, $tagName)
    {
        $targetHeading = $sourceHeading->parentNode->ownerDocument->createElement($tagName);
        self::copyAttributes($sourceHeading, $targetHeading);
        self::moveChildNodes($sourceHeading, $targetHeading);

        return $targetHeading;
    }

    private static function copyAttributes(\DOMElement $source, \DOMElement $target)
    {
        foreach ($source->attributes as $attribute) {
            $target->setAttribute($attribute->name, $attribute->value);
        }
    }

    private static function moveChildNodes(\DOMElement $source, \DOMElement $target)
    {
        while ($source->hasChildNodes()) {
            // appendChild() actually moves the childNode
            $target->appendChild($source->childNodes->item(0));
        }
    }
}
