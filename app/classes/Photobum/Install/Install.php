<?php

namespace Photobum\Install;

use Photobum\Utilities\General;
use Photobum\Utilities\Mapper;

class Install extends InstallController {

  public function start() {

    $template = $this->twig->loadTemplate('Install/start.html');
    echo $template->render([
      'page' => $this->page
    ]);
  }
}
