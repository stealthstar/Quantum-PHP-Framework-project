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
 * @since 1.9.9
 */

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Loader\Loader;

if (!function_exists('loadUsers')) {
    /**
     * Load users
     * @return mixed
     * @throws Exception
     */
    function loadUsers()
    {
        $loaderSetup = (object)[
            'module' => null,
            'hierarchical' => true,
            'env' => 'base' . DS . 'repositories',
            'fileName' => 'users',
            'exceptionMessage' => ExceptionMessages::CONFIG_FILE_NOT_FOUND
        ];

        return (new Loader(new FileSystem))->setup($loaderSetup)->load();
    }
}