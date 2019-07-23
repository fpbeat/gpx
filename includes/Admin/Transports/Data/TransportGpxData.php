<?php

namespace MapRoute\Admin\Transports\Data;

use MapRoute\Utils\Arr;
use phpGPX\Helpers\GeoHelper;
use phpGPX\Models\Point;

class TransportGpxData extends TransportAbstractData implements TransportDataInterface {

    private $points = [];
    private $segments = [];
    private $meta = [];

    public function __construct(array $data) {
        foreach (['points', 'meta', 'segments'] as $name) {
            $this->$name = Arr::get($data, $name, []);
        }
    }

    public function getPoints() {
        $startPoint = reset($this->points);

        $points = [];
        foreach ($this->points as $index => $point) {
            $points[] = Arr::extract($point->toArray(), ['lat', 'lon', 'ele', 'time']) + [
                    'distance' => $startPoint instanceof Point ? round(GeoHelper::getDistance($point, $startPoint), 2) : 0
                ];
        }

        return $points;
    }

    public function getAgvSpeed() {
        $speed = Arr::pluck($this->segments, 'avgSpeed');

        if (count($speed) > 0) {
            return round(array_sum($speed) / count($speed), 2);
        }

        return 0;
    }

    public function getMinAltitude() {
        $altitude = Arr::pluck($this->segments, 'minAltitude');

        if (count($altitude) > 0) {
            return min($altitude);
        }

        return 0;
    }

    public function getMaxAltitude() {
        $altitude = Arr::pluck($this->segments, 'maxAltitude');

        if (count($altitude) > 0) {
            return min($altitude);
        }

        return 0;
    }

    public function getDuration() {
        $duration = Arr::pluck($this->segments, 'duration');

        return array_sum($duration);
    }

    public function getIdleTime() {
        print_r($this->segments);
    }

    public function getMeta() {
        return $this->meta;
    }
}