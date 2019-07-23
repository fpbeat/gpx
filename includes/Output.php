<?php

namespace MapRoute;

use MapRoute\Admin\Settings;
use MapRoute\Admin\Transport;
use MapRoute\Utils\Arr;
use MapRoute\Utils\Text;

class Output {

    private $fenom;

    const BOOLEAN_ATTRIBUTES = ['map-placemarks', 'map-hint', 'chart-zoom'];

    public function __construct() {
        $this->fenom = Registry::instance()['fenom'];
    }

    public function getRouteData(array $attributes, $data = NULL) {
        foreach ($attributes as $name => $value) {
            if (in_array($name, Transport::SUPPORTED) && is_array($data) && array_key_exists(md5($value), $data)) {
                return $data[md5($value)];
            }
        }

        return NULL;
    }

    private function parseBooleanAttributes(array $attributes) {
        return Arr::arrayBothMap(function ($name, $value) {
            if (in_array($name, self::BOOLEAN_ATTRIBUTES)) {
                return [$name, in_array(strval($value), ['true', 'yes', '1', 'Y'])];
            }

            return [$name, $value];
        }, $attributes);
    }

    public function render($attributes) {
        $data = $this->getRouteData($attributes, get_post_meta(get_the_ID(), sprintf('%s_data', Registry::instance()['token']), TRUE));
        $settings = shortcode_atts(Settings::instance()->getAllOptions(), $this->parseBooleanAttributes($attributes));

        if ($data !== NULL) {
            $formatter = new TemplateFormatter($data, $settings);

            return $this->fenom->fetch('web/output.tpl', [
                'token' => Registry::instance()['token'],
                'hash' => Text::getRandomHash(),
                'data' => $data,
                'settings' => Arr::camelCase($settings),
                'template' => $formatter->format($settings['template'])
            ]);
        }

        return $this->fenom->fetch('web/empty.tpl');
    }
}