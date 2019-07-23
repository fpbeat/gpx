<?php

namespace MapRoute;

class Registry implements \ArrayAccess {

    static private $instance = NULL;
    private $vars = [];

    public static function instance() {
        if (self::$instance == NULL) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {

    }

    public function __clone() {
        throw new \Exception('Cloning not allowed');
    }

    public function __set($index, $value) {
        $this->add($index, $value);
    }

    public function __get($index) {
        return $this->get($index);
    }

    public function add($name, $item = NULL, $overwrite = TRUE) {
        if (is_array($name) && is_null($item)) {
            foreach ($name as $key => $value) {
                $this->add($key, $value, $overwrite);
            }
        } else {
            if ($overwrite) {
                $this->vars[$name] = $item;
            } else {
                if (!$this->exists($name)) {
                    $this->vars[$name] = $item;
                }
            }
        }

    }

    public function exists($name) {
        return array_key_exists($name, $this->vars);
    }

    public function get($name, $default = NULL) {
        if ($this->exists($name)) {
            return $this->vars[$name];
        }

        return $default;
    }

    public function remove($name) {
        if ($this->exists($name)) {
            unset($this->vars[$name]);
        }
    }

    public function clear() {
        $this->vars = [];
    }

    public function all() {
        return $this->vars;
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        $this->add($offset, $value);
    }

    public function offsetExists($offset) {
        return $this->exists($offset);
    }


    public function offsetUnset($offset) {
        $this->remove($offset);
    }
}
