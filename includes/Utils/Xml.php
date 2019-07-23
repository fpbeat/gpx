<?php

namespace MapRoute\Utils;

class Xml {

    public static function isValid($content) {
        libxml_use_internal_errors(TRUE);

        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->loadXML($content);

        $errors = libxml_get_errors();
        libxml_clear_errors();

        return count($errors) === 0;
    }
}