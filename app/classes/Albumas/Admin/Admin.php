<?php

namespace Albumas\Admin;

use Albumas\Base;
use Albumas\Config;
use Albumas\Utilities\Twig;

class Admin extends Base
{

    public function __construct()
    {

        parent::__construct();

  

        $this->twig = new Twig();
        $this->page = array(
            'base_url' => Config::get('BASE_URL'),
            'environment' => Config::get('ENVIRONMENT'),
            'session' => $_SESSION,
            'approot' => '/admin/',
            'path' => $this->f3->get('PATH'),
            'section' => 'home',
            'user' => $this->f3->get('SESSION.cw_cms_admin')
        );
    }

    public function home()
    {
        $this->auth();

        $template = $this->twig->loadTemplate('Admin/home.html');
        echo $template->render(array(
            'page' => $this->page,
        ));

    }

    public function auth($level = 0)
    {
        if ($this->f3->get('SESSION.cw_cms_admin')) {
            if ($this->f3->get('SESSION.cw_cms_admin.access_level') >= $level) {    
                return 1;
            }
            $this->f3->error(403);
            $this->f3->reroute('/admin/');

        }
        $this->f3->reroute('/admin/login');
    }

}
