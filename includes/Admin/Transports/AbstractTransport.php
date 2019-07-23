<?php

namespace MapRoute\Admin\Transports;

abstract class AbstractTransport {

    protected $content;
    protected $params;

    public function __construct($params, $content) {
        $this->params = $params;
        $this->content = $content;

        if (!$this->isValid()) {
            throw new \Exception('Error validating GPX file');
        }
    }

    public function getUrl() {
        return $this->params['url'];
    }

    abstract public function parse();

    abstract public function isValid();
}
