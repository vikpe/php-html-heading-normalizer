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

        $headingTagNames = ['h1', 'h2', 'h3', 'h4', 'h6'];

        $originalHeadings = [];
        $normalizedHeadings = [];

        foreach ($headingTagNames as $headingTagName) {
            $headingDomElements = $domDocument->getElementsByTagName($headingTagName);

            foreach ($headingDomElements as $headingDomElement) {
                $currentHeadingLevel = self::headingTagNameToNumber($headingDomElement->tagName);
                $newHeadingLevel = self::numberToHeadingLevel($baseLevel + $currentHeadingLevel - 1);

                if ($newHeadingLevel !== $currentHeadingLevel) {
                    $originalHeadings[] = $headingDomElement;
                    $normalizedHeadings[] = self::cloneDomElementWithNewTagName($headingDomElement, $newHeadingLevel);
                }
            }
        }

        foreach ($originalHeadings as $i => $needle) {
            $needle->parentNode->replaceChild($normalizedHeadings[$i], $needle);
        }

        return $domDocument->saveHTML();
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
