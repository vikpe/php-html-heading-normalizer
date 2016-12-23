<?php

namespace Vikpe;

class HtmlHeadingNormalizer
{
    public static function normalize($html, $baseLevel = 1)
    {
        if (!self::htmlContainsHeadings($html)) {
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
            $currentHeadingLevel = self::headingTagNameToNumber($heading->tagName);
            $newHeadingLevel = self::numberToHeadingLevel($baseLevel + $currentHeadingLevel - 1);

            $normalizedHeadings[] = self::cloneDomElementWithNewTagName($heading, $newHeadingLevel);
        }

        return $normalizedHeadings;
    }

    private static function htmlContainsHeadings($html)
    {
        $headingNeedle = '<h';
        $containsHeadings = (false !== stripos($html, $headingNeedle));

        return $containsHeadings;
    }

    private static function headingTagNameToNumber($headingLevel)
    {
        return substr($headingLevel, 1);
    }

    private static function numberToHeadingLevel($number)
    {
        return 'h'.$number;
    }

    private static function cloneDomElementWithNewTagName(\DOMElement $sourceDomElement, $newTagName)
    {
        $targetDomElement = $sourceDomElement->parentNode->ownerDocument->createElement($newTagName);
        self::copyAttributes($sourceDomElement, $targetDomElement);
        self::moveChildNodes($sourceDomElement, $targetDomElement);

        return $targetDomElement;
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
