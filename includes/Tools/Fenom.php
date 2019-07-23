<?php

namespace MapRoute\Tools;

use MapRoute\Registry;
use MapRoute\Utils\Arr;

class Fenom {

    const VIEWS_DIRECTORY = 'views';
    const CACHE_DIRECTORY = 'cache';
    const COMPILED_DIRECTORY = 'cache/compiled';

    private static $instance = [];

    public static function instance(array $options = []) {
        $hash = Arr::getHash($options);

        if (!isset(self::$instance[$hash])) {
            self::$instance[$hash] = new self($options);
        }

        return self::$instance[$hash];
    }

    public function __construct(array $options) {
        $this->fenom = \Fenom::factory(Registry::instance()['dir'] . DIRECTORY_SEPARATOR . self::VIEWS_DIRECTORY, Registry::instance()['dir'] . DIRECTORY_SEPARATOR . self::COMPILED_DIRECTORY, $options);
        $this->getRegisterFenomModificators();
    }


    private function getRegisterFenomModificators() {
        $this->fenom->addModifier('default', function ($variable, $default = '') {
            return empty($variable) ? $default : $variable;
        });
    }

    public function __call($method, $params) {
        if (method_exists($this->fenom, $method)) {
            return call_user_func_array([$this->fenom, $method], $params);
        }

        throw new \Exception(sprintf('Unknown Fenom method -  %s ', $method));
    }
}