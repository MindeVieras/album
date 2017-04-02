<?php

namespace Photobum\Install;

use Photobum\Base;
use Photobum\Config;
use Photobum\Utilities\Twig;

class InstallController extends Base
{
    public $page, $twig, $isAuthed;
    
    public function __construct()
    {
        parent::__construct();
        $this->twig = new Twig();
        $this->page = [
            'base_url' => Config::get('BASE_URL'),
            'path' => $this->f3->get('PATH'),
            'isAuthed' => $this->isAuthed
        ];
    }
}
