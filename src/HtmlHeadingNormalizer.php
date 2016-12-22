<?php

namespace Vikpe;

class HtmlHeadingNormalizer
{
    public static function normalize($html, $base_level = 0)
    {
        if (!self::_htmlContainsHeadings($html)) {
            return $html;
        }

        $DomDocument = new \DOMDocument();
        $DomDocument->loadHTML($html);

        $normalized_html = $DomDocument->saveHTML();

        return $normalized_html;
    }

    private static function _htmlContainsHeadings($html)
    {
        $heading_needle = '<h';
        $html_contains_headings = (false === stripos($html, $heading_needle));

        return $html_contains_headings;
    }
}
