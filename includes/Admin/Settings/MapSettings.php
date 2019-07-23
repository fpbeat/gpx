<?php

namespace MapRoute\Admin\Settings;

use MapRoute\Registry;

class MapSettings extends AbstractSettings {

    const USED_OPTIONS = ['map-api-key', 'map-enabled', 'map-width', 'map-height', 'map-type', 'map-route-color', 'map-placemarks', 'map-placemark-color', 'map-hint', 'map-hint-fields'];

    private $presetColors = [
        'blueDotIcon' => ['#2398FD'],
        'redDotIcon' => ['#EC4442'],
        'darkOrangeDotIcon' => ['#E5751E'],
        'nightDotIcon' => ['#164B7C'],
        'darkBlueDotIcon' => ['#1A7BC7'],
        'pinkDotIcon' => ['#F371CF'],
        'grayDotIcon' => ['#B2B2B2'],
        'brownDotIcon' => ['#783C0F'],
        'darkGreenDotIcon' => ['#15AC12'],
        'violetDotIcon' => ['#B524FD'],
        'blackDotIcon' => ['#585858'],
        'yellowDotIcon' => ['#FED94D'],
        'greenDotIcon' => ['#53DA44'],
        'orangeDotIcon' => ['#FE9223'],
        'lightBlueDotIcon' => ['#82CDFE'],
        'oliveDotIcon' => ['#96A00F'],
    ];

    protected function setTab() {
        $this->mapTab = $this->panel->createTab([
            'id' => sprintf('%s_map', Registry::instance()['token']),
            'name' => 'Настройки карты',
        ]);
    }

    public function create() {
        $this->mapTab->createOption([
            'name' => 'Ключ API карт',
            'id' => 'map-api-key',
            'type' => 'text'
        ]);

        $this->mapTab->createOption([
            'name' => 'Ширина карты',
            'id' => 'map-width',
            'desc' => 'В шорткоде «map-width»',
            'type' => 'number',
            'default' => 400,
            'step' => 5,
            'min' => 100,
            'max' => 1200,
        ]);

        $this->mapTab->createOption([
            'name' => 'Высота карты',
            'id' => 'map-height',
            'desc' => 'В шорткоде «map-height»',
            'type' => 'number',
            'default' => 300,
            'step' => 5,
            'min' => 50,
            'max' => 1200,
        ]);

        $this->mapTab->createOption([
            'name' => 'Тип карты по умолчанию',
            'id' => 'map-type',
            'desc' => 'В шорткоде «map-type»',
            'type' => 'radio',
            'options' => [
                'yandex#map' => 'Схема',
                'yandex#satellite' => 'Спутник',
                'yandex#hybrid' => 'Гибрид',
            ],
            'default' => 'yandex#satellite',
        ]);

        $this->mapTab->createOption([
            'name' => 'Цвет маршрута',
            'id' => 'map-route-color',
            'desc' => 'В шорткоде «map-route-color»',
            'type' => 'color',
            'default' => '#E5751E',
        ]);

        $this->mapTab->createOption([
            'id' => 'map-placemark-caption',
            'name' => 'Маркеры',
            'type' => 'heading',
        ]);

        $this->mapTab->createOption([
            'name' => 'Маркеры маршрута',
            'id' => 'map-placemarks',
            'desc' => 'В шорткоде «map-placemarks»',
            'type' => 'enable',
            'default' => TRUE,
        ]);

        $this->mapTab->createOption([
            'name' => 'Цвет маркеров',
            'id' => 'map-placemark-color',
            'desc' => 'В шорткоде «map-placemark-color»',
            'type' => 'radio-palette',
            'options' => array_values($this->presetColors),
            'default' => 0,
        ]);

        $this->mapTab->createOption([
            'id' => 'map-hint-caption',
            'name' => 'Информация о точках',
            'type' => 'heading',
        ]);

        $this->mapTab->createOption([
            'name' => 'Информация',
            'id' => 'map-hint',
            'desc' => 'В шорткоде «map-hint»',
            'type' => 'enable',
            'default' => TRUE,
        ]);

        $this->mapTab->createOption([
            'name' => 'Показывать поля',
            'id' => 'map-hint-fields',
            'desc' => 'В шорткоде «map-hint-fields»',
            'type' => 'multicheck',
            'options' => [
                'time' => 'дата',
                'distance' => 'расстояние',
                'ele' => 'высота',
            ],
            'default' => ['time', 'distance', 'ele'],
        ]);

        $this->mapTab->createOption(array(
            'type' => 'save'
        ));
    }

    public function getExtraOption($name, $value) {
        switch ($name) {
            case 'map-placemark-color':
                if (is_array($value)) {
                    $firstColor = reset($value);

                    foreach ($this->presetColors as $name => $color) {
                        if (in_array($firstColor, $color)) {
                            return $name;
                        }
                    }

                    return reset(array_keys($this->presetColors));
                }
                break;
        }

        return NULL;
    }
}