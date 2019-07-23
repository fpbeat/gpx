<?php

namespace MapRoute\Admin\Settings;

use MapRoute\Registry;

class ChartSettings extends AbstractSettings {

    const USED_OPTIONS = ['chart-width', 'chart-height', 'chart-name', 'chart-line-color', 'chart-base-color', 'chart-zoom'];

    protected function setTab() {
        $this->mapTab = $this->panel->createTab([
            'id' => sprintf('%s_chart', Registry::instance()['token']),
            'name' => 'Настройки диаграммы',
        ]);
    }

    public function create() {
        $this->mapTab->createOption([
            'name' => 'Ширина диаграммы',
            'id' => 'chart-width',
            'desc' => 'В шорткоде «chart-width»',
            'type' => 'number',
            'default' => 400,
            'step' => 5,
            'min' => 100,
            'max' => 1200,
        ]);

        $this->mapTab->createOption([
            'name' => 'Высота диаграммы',
            'id' => 'chart-height',
            'desc' => 'В шорткоде «chart-height»',
            'type' => 'number',
            'default' => 300,
            'step' => 5,
            'min' => 50,
            'max' => 1200,
        ]);

        $this->mapTab->createOption([
            'name' => 'Название диаграммы',
            'id' => 'chart-name',
            'desc' => 'В шорткоде «chart-name»',
            'type' => 'textarea'
        ]);

        $this->mapTab->createOption([
            'id' => 'chart-heading-colors',
            'name' => 'Цвета',
            'type' => 'heading',
        ]);

        $this->mapTab->createOption([
            'name' => 'Цвет лини',
            'id' => 'chart-line-color',
            'desc' => 'В шорткоде «chart-line-color»',
            'type' => 'color',
            'default' => '#434348',
        ]);

        $this->mapTab->createOption([
            'name' => 'Цвет заливки',
            'id' => 'chart-base-color',
            'desc' => 'В шорткоде «chart-base-color»',
            'type' => 'color',
            'default' => '#90ed7d',
        ]);

        $this->mapTab->createOption([
            'id' => 'chart-heading-texts',
            'name' => 'Другое',
            'type' => 'heading',
        ]);

        $this->mapTab->createOption([
            'name' => 'Зумирование',
            'id' => 'chart-zoom',
            'desc' => 'В шорткоде «chart-zoom»',
            'type' => 'enable',
            'default' => TRUE,
        ]);

        $this->mapTab->createOption([
            'type' => 'save'
        ]);
    }
}