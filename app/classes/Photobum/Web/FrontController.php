<?php

namespace Photobum\Web;

use Photobum\Base;
use Photobum\Config;
use Photobum\Utilities\Twig;

class FrontController extends Base
{
    public $page, $twig, $isAuthed;
    
    public function __construct()
    {
        parent::__construct();
        $this->twig = new Twig();
        $this->isAuthed =  (bool) $this->f3->get('SESSION.cw_cms_admin');
        //$this->twig->onReady('Creation.initFrontend');
        $this->page = [
            'base_url' => Config::get('BASE_URL'),
            'environment' => Config::get('ENVIRONMENT'),
            'path' => $this->f3->get('PATH'),
            'isAuthed' => $this->isAuthed
        ];
    }

}
