<?php

namespace MapRoute\Admin\Transports\Data;

abstract class TransportAbstractData {

    public function toArray() {
        return [
            'points' => $this->getPoints(),
            'meta' => $this->getMeta(),
            'agvSpeed' => $this->getAgvSpeed(),
            'minAltitude' => $this->getMinAltitude(),
            'maxAltitude' => $this->getMaxAltitude(),
            'duration' => $this->getDuration()
        ];
    }
}