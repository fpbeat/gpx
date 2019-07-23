<?php

namespace MapRoute\Admin\Transports\Data;

interface TransportDataInterface {

    public function getPoints();

    public function getAgvSpeed();

    public function getMinAltitude();

    public function getMaxAltitude();

    public function getDuration();

    public function getIdleTime();

    public function getMeta();
}