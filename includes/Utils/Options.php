<?php

namespace MapRoute\Utils;

use MapRoute\Registry;

class Options {

    public static function all() {
        $options = [];

        foreach (wp_load_alloptions() as $name => $value) {
            if (strpos($name, sprintf('%s_', Registry::instance()['token'])) === 0) {
                $options[preg_replace('/^' . sprintf('%s_', Registry::instance()['token']) . '/', '', $name)] = maybe_unserialize($value);
            }
        }

        return $options;
    }

    public static function get($name, $default = NULL) {
        return Arr::get(self::all(), $name, $default);
    }

    public static function update($name, $value) {
        return update_option(sprintf('%s_%s', Registry::instance()['token'], $name), $value);
    }

    public static function delete($option) {
        return delete_option(sprintf('%s_%s', Registry::instance()['token'], $option));
    }
}