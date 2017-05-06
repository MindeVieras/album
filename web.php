<?php
// phpinfo();
// die();
$loader = require 'vendor/autoload.php';
$loader->add('', 'app/classes');

\Photobum\Config::bootstrap('web');

$f3 = \Base::instance();

$f3->route('GET|HEAD /', '\Photobum\Web\Home->view');

$f3->route('GET|HEAD /@year/@month/@day/@slug', '\Photobum\Web\Albums->viewOne');

$f3->run();
