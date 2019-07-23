<?php

namespace MapRoute;

use MapRoute\Utils\Arr;
use MapRoute\Utils\Date;
use MapRoute\Utils\Text;

use Moment\Moment;

class TemplateFormatter {

    private $data = [];
    private $settings = [];

    public function __construct($data, array $settings) {
        $this->data = $data;
        $this->settings = Arr::camelCase($settings);
    }

    public function format($text) {
        return Text::substitute($text, $this->toArray());
    }

    public function getMapComponent() {
        return Registry::instance()['fenom']->fetch('web/components/map.tpl', [
            'settings' => $this->settings
        ]);
    }

    public function getChartComponent() {
        return Registry::instance()['fenom']->fetch('web/components/chart.tpl', [
            'settings' => $this->settings
        ]);
    }

    public function getDistance() {
        $distance = 0;

        foreach ($this->data['points'] as $point) {
            $distance = +$point['distance'];
        }

        return $distance >= 1000 ? sprintf('%s км.', round($distance / 1000, 1)) : sprintf('%s м.', $distance);
    }

    public function getDuration() {
        return Date::secondsToTime(intval($this->data['duration']));
    }

    public function getName() {
        return Arr::get($this->data['meta'], 'name', '-');
    }

    public function getCreator() {
        return Arr::get($this->data['meta'], 'creator', '-');
    }

    public function getDescription() {
        return Arr::get($this->data['meta'], 'description', '-');
    }

    public function getDatetime() {
        $dateTime = Arr::get($this->data['meta'], 'time');

        try {
            Moment::setLocale('ru_RU');

            $m = new Moment($dateTime);

            return !empty($dateTime) ? $m->format('d F Y г. H:i') : '-';
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function getAgvSpeed() {
        $value = floatval($this->data['agvSpeed']);

        return $value > 0 ? sprintf('%s км/ч', round($value * 3.6, 1)) : '-';
    }

    public function getMinAltitude() {
        $value = floatval($this->data['minAltitude']);

        return $value > 0 ? sprintf('%s м.', round($value, 0)) : '-';
    }

    public function getMaxAltitude() {
        $value = floatval($this->data['maxAltitude']);

        return $value > 0 ? sprintf('%s м.', round($value, 0)) : '-';
    }

    public function getDownloadLink() {
        return Arr::get($this->data['meta'], 'url', '');
    }

    public function toArray() {
        return [
            'map' => $this->getMapComponent(),
            'chart' => $this->getChartComponent(),
            'distance' => $this->getDistance(),
            'duration' => $this->getDuration(),
            'name' => $this->getName(),
            'creator' => $this->getCreator(),
            'datetime' => $this->getDatetime(),
            'agv-speed' => $this->getAgvSpeed(),
            'min-altitude' => $this->getMinAltitude(),
            'max-altitude' => $this->getMaxAltitude(),
            'download-link' => $this->getDownloadLink()
        ];
    }

}