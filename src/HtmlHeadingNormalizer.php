<?php

namespace Vikpe;

class HtmlHeadingNormalizer
{
    public static function demote($html, $levels)
    {
        return self::normalize($html, $levels);
    }

    public static function promote($html, $levels)
    {
        return self::normalize($html, -$levels);
    }

    private static function normalize($html, $levels)
    {
        if (!self::containsHeadings($html)) {
            return $html;
        }

        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($html);

        $originalHeadings = self::getHeadings($domDocument);
        $normalizedHeadings = self::normalizeHeadings($originalHeadings, $levels);

        self::replaceHeadings(
            $originalHeadings,
            $normalizedHeadings
        );

        return $domDocument->saveHTML();
    }

    private static function getHeadings(\DOMDocument $domDocument)
    {
        $tagNames = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');

        $headings = array();

        foreach ($tagNames as $tagName) {
            foreach ($domDocument->getElementsByTagName($tagName) as $heading) {
                $headings[] = $heading;
            }
        }

        return $headings;
    }

    private static function normalizeHeadings(array $originalHeadings, $levelDelta)
    {
        $normalizedHeadings = array();

        foreach ($originalHeadings as $heading) {
            $currentLevel = self::tagNameToLevel($heading->tagName);
            $normalizedLevel = $currentLevel + $levelDelta;

            $normalizedHeadings[] = self::cloneHeading($heading, $normalizedLevel);
        }

        return $normalizedHeadings;
    }

    private static function replaceHeadings(array $needles, array $replacements)
    {
        foreach ($needles as $i => $needle) {
            $needle->parentNode->replaceChild($replacements[$i], $needle);
        }
    }

    private static function containsHeadings($html)
    {
        $headingNeedle = '<h';
        $containsHeadings = (false !== stripos($html, $headingNeedle));

        return $containsHeadings;
    }

    private static function tagNameToLevel($tagName)
    {
        return substr($tagName, 1);
    }

    private static function levelToTagName($level)
    {
        return 'h'.$level;
    }

    private static function cloneHeading(\DOMElement $sourceHeading, $level = null)
    {
        if (null !== $level) {
            $tagName = self::levelToTagName($level);
        } else {
            $tagName = $sourceHeading->tagName;
        }

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
