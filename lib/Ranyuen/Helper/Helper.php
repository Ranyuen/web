<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Helper;

/**
 * Basic view helper.
 */
abstract class Helper
{
    /**
     * Escape all HTML5 special charactors.
     *
     * @param string $str Raw string
     *
     * @return string
     */
    protected function html($str)
    {
        if (is_array($str)) {
            $str = implode(', ', $str);
        } elseif (!is_string($str)) {
            $str = (string) $str;
        }

        return htmlspecialchars(
            $str,
            ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5,
            'utf-8'
        );
    }
}
