<?php

namespace MapRoute\Utils;

class Text {

    public static function getRandomHash() {
        $hash = md5(sprintf('%s-%s', mt_rand(0, mt_getrandmax()), uniqid(TRUE)));

        return substr($hash, 0, 12);
    }

    public static function camelCase($string) {
        return preg_replace_callback('/-\D/i', function ($match) {
            return strtoupper(substr($match[0], 1, 1));
        }, $string);
    }

    public static function substitute($text, $object, $regexp = '/~?\{([^{}]+)\}/u') {
        return preg_replace_callback($regexp, function ($match) use ($object) {
            if (substr($match[1], 0, 1) === '~') {
                return $match[0];
            }

            return ($object[$match[1]] !== NULL) ? $object[$match[1]] : '';
        }, $text);
    }

    public static function plural($n) {
        $plural = ($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 or $n % 100 >= 20) ? 1 : 2));
        $form = func_num_args() === 4 ? array_slice(func_get_args(), 1) : func_get_arg(1);

        switch ($plural) {
            case 0:
            default:
                return $form[0];
            case 1:
                return $form[1];
            case 2:
                return $form[2];
        }
    }
}