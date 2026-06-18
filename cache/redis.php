<?php

require_once __DIR__ . '/../vendor/autoload.php';

function redis() {

    static $redis = null;

    if ($redis === null) {

        $redis = new Predis\Client([
            "scheme" => "tcp",
            "host" => "127.0.0.1",
            "port" => 6379
        ]);
    }

    return $redis;
}