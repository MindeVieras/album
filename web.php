<?php

$loader = require 'vendor/autoload.php';
$loader->add('', 'app/classes');

\Photobum\Config::bootstrap('web');

$f3 = \Base::instance();

$f3->route('GET|HEAD /', '\Photobum\Web\Home->view');

$f3->route('GET|HEAD /albums', '\Photobum\Web\Albums->view');
$f3->route('GET|HEAD /albums/@slug', '\Photobum\Web\Albums->viewOne');

$f3->route('GET|HEAD /info', '\Photobum\Web\Info->view');


$f3->run();
