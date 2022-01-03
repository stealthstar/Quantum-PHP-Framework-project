<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.6.0
 */

if (!function_exists('auto_increment')) {

    /**
     * Auto increment
     * @param array $collection
     * @param string $field
     * @return int|mixed
     */
    function auto_increment(array $collection, string $field)
    {
        $max = 0;
        foreach ($collection as $item) {
            $max = max($max, $item[$field]);
        }
        return ++$max;
    }
}

if (!function_exists('url_with_lang')) {

    /**
     * Gets the url with selected language
     * @param string $lang
     * @return string
     */
    function url_with_lang(string $lang): string
    {
        $url = base_url();

        if (trim(route_uri()) == '/') {
            $url .= '/' . $lang;
        } else {
            $url .= '/' . preg_replace("/" . preg_quote("/" . current_lang(), '/') . "/", $lang, route_uri(), 1);
        }

        return $url;
    }

}