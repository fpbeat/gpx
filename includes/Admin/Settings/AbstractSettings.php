<?php

namespace MapRoute\Admin\Settings;

abstract class AbstractSettings {

    protected $panel;
    protected $mapTab;

    public function __construct(\TitanFrameworkAdminPage $panel) {
        $this->panel = $panel;

        $this->setTab();
    }

    public function getExtraOption($name, $value) {
        return NULL;
    }

    abstract protected function setTab();

    abstract public function create();
}
