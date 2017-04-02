<?php

$loader = require 'vendor/autoload.php';
$loader->add('', 'app/classes');

\Photobum\Config::bootstrap('api');
$f3 = \Base::instance();

$f3->map('/api/image', '\Photobum\API\Image');

$f3->map('/api/test', '\Photobum\API\Test');

$f3->route('GET /api/utilities/generateslug', '\Photobum\API\Utilities->generateSlug');
$f3->route('POST /api/utilities/collapse-menu', '\Photobum\API\Utilities->collapseMenu');
$f3->route('POST /api/utilities/delete-files', '\Photobum\API\Utilities->deleteAlbumDir');

// Installr API
$f3->route('POST /api/installer/composer-get-status', '\Photobum\API\Installer->composerCheckStatus');

$f3->run();