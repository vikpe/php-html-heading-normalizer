<?php

namespace Vikpe;

class HtmlHeadingNormalizer
{
    public static function normalize($html)
    {
        if (!self::htmlContainsHeadings($html)) {
            return $html;
        }

        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($html);

        $normalizedHtml = $domDocument->saveHTML();

        return $normalizedHtml;
    }

    private static function htmlContainsHeadings($html)
    {
        $headingNeedle = '<h';
        $containsHeadings = (false === stripos($html, $headingNeedle));

        return $containsHeadings;
    }
}
