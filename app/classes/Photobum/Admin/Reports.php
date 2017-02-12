<?php

namespace Photobum\Admin;


use Photobum\Utilities\General;
use DB\SQL\Mapper;
use Photobum\Config;

class Reports extends Admin{

    public function __construct(){
        parent::__construct();
        $this->page['section']= 'reports';
    }

    public function view($params){
        $this->auth();
        $template = $this->twig->loadTemplate('Admin/reports.html');
        echo $template->render([
            'page' => $this->page,
            'user' => $this->user
        ]);
    }

}