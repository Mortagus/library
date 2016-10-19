<?php

namespace Mortagus\Library\Intern;

class Autoloader {

    static function register() {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    static function autoload($className) {
        $filename = str_replace('Mortagus\\', '', $className) . '.php';
        if (file_exists($filename)) {
            require_once $filename;
        }
    }

}
