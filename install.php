<?php

$loader = require 'vendor/autoload.php';
$loader->add('', 'app/classes');

\Photobum\Config::bootstrap('install');

$f3 = \Base::instance();

$f3->route('GET|HEAD /install', '\Photobum\Install\Install->start');

$f3->run();
