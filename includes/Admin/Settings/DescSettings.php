<?php

namespace MapRoute\Admin\Settings;

use MapRoute\Registry;

class DescSettings extends AbstractSettings {

    const USED_OPTIONS = ['template'];
    const DEFAULT_TEMPLATE = '<p>{map}</p><p>{chart}</p>';

    public function __construct(\TitanFrameworkAdminPage $panel) {
        parent::__construct($panel);

        $this->fenom = Registry::instance()['fenom'];
    }

    protected function setTab() {
        $this->mapTab = $this->panel->createTab([
            'id' => sprintf('%s_desc', Registry::instance()['token']),
            'name' => 'Шаблон',
        ]);
    }

    public function create() {
        $this->mapTab->createOption([
            'name' => 'Шаблон описания',
            'id' => 'template',
            'desc' => 'В шорткоде «template»',
            'media_buttons' => FALSE,
            'type' => 'editor',
            'default' => self::DEFAULT_TEMPLATE,
            'editor_settings' => [
                'teeny' => TRUE
            ]
        ]);

        $this->mapTab->createOption([
            'type' => 'note',
            'name' => 'Параметры шаблона',
            'desc' => $this->fenom->fetch('web/settings/params.tpl')
        ]);

        $this->mapTab->createOption([
            'type' => 'save'
        ]);
    }
}