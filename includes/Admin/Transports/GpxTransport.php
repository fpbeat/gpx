<?php

namespace MapRoute\Admin\Transports;

use MapRoute\Admin\Transports\Data\TransportGpxData;
use MapRoute\Utils\Arr;
use MapRoute\Utils\Xml;
use phpGPX\phpGPX;

class GpxTransport extends AbstractTransport {

    public function parse() {
        $gpx = new phpGPX();
        $file = $gpx->parse($this->content);

        $trackName = NULL;
        $data = ['segments' => [], 'points' => [], 'meta' => []];

        foreach ($file->tracks as $index => $track) {
            if ($index === 0) {
                $trackName = $track->name;
            }

            foreach ($track->segments as $segment) {
                array_push($data['segments'], Arr::extract($segment->stats->toArray(), ['cumulativeElevationGain', 'avgSpeed', 'minAltitude', 'maxAltitude', 'avgPace', 'duration', 'startedAt', 'finishedAt']));
            }

            $data['points'] = $track->getPoints();
        }

        $data['meta'] = [
            'url' => $this->getUrl(),
            'name' => $trackName,
            'creator' => $file->creator,
            'description' => $file->metadata->description,
            'time' => $file->metadata->time instanceof \DateTime ? $file->metadata->time->format('Y-m-d H:i:s') : NULL
        ];

        return new TransportGpxData($data);
    }

    public function isValid() {
        return Xml::isValid($this->content);
    }
}