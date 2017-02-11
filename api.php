<?php

$loader = require 'vendor/autoload.php';
$loader->add('', 'app/classes');

\Photobum\Config::bootstrap('api');
$f3 = \Base::instance();

$f3->map('/api/image', '\Photobum\API\Image');

$f3->map('/api/test', '\Photobum\API\Test');

$f3->route('GET /api/utilities/generateslug', '\Photobum\API\Utilities->generateSlug');
$f3->route('POST /api/utilities/get-file-size', '\Photobum\API\Utilities->getFileSize');
$f3->route('POST /api/utilities/rename-files', '\Photobum\API\Utilities->renameFiles');
$f3->route('POST /api/utilities/get-file-size-remote', '\Photobum\API\Utilities->getFileSizeRemote');
$f3->run();