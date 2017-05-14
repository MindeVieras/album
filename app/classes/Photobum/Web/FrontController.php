<?php

namespace Photobum\Web;

use Photobum\Base;
use Photobum\Config;
use Photobum\Utilities\Twig;

class FrontController extends Base
{    
    public function __construct()
    {
        parent::__construct();

        $this->twig = new Twig();
        $user = $this->f3->get('SESSION.album_web');
        //$this->twig->onReady('Creation.initFrontend');
        $this->page = [
            'base_url' => Config::get('BASE_URL'),
            'environment' => Config::get('ENVIRONMENT'),
            'path' => $this->f3->get('PATH'),
            'base_path' => rtrim(Config::get('BASE_PATH'), '/'),
            'session' => $_SESSION,
            'approot' => '/',
            'path' => $this->f3->get('PATH'),
            'title' => 'Homepage',
            'section' => 'homepage',
            'user' => $user,
        ];
    }

    public function auth($level = 0)
    {
        if ($this->f3->get('SESSION.album_web')) {
            if ($this->f3->get('SESSION.album_web.access_level') >= $level) {    
                return 1;
            }
            $this->f3->error(403);
            $this->f3->reroute('/');

        }
        $this->f3->reroute('/login');
    }

}
