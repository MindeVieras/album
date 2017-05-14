<?php

namespace Photobum\Admin;

use Photobum\Base;
use Photobum\Config;
use Photobum\Utilities\Twig;

class Admin extends Base
{

    public function __construct()
    {

        parent::__construct();

        $user = $this->f3->get('SESSION.cw_cms_admin');
        $uid = $user['id'];
        
        $this->twig = new Twig();
        $this->page = array(
            'base_url' => Config::get('BASE_URL'),
            'base_path' => rtrim(Config::get('BASE_PATH'), '/'),
            'environment' => Config::get('ENVIRONMENT'),
            'session' => $_SESSION,
            'approot' => '/admin/',
            'path' => $this->f3->get('PATH'),
            'title' => 'Dashboard',
            'device' => $this->getDevice(),
            'section' => 'dashboard',
            'user' => $user,
            'user_settings' => $this->db->exec("SELECT * FROM user_settings WHERE user_id = '$uid'")
        );
        //ddd($this);
    }

    public function home()
    {
        $this->auth(25);

        $template = $this->twig->loadTemplate('Admin/dashboard.html');
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
    
    private function getDevice(){

        $detect = new \Mobile_Detect;
        
        $device = 'desktop';
        if ( $detect->isMobile() ) {
            $device = 'mobile';
        }
         
        if( $detect->isTablet() ){
            $device = 'tablet';
        }
        //ddd($detect);
        return $device;
    }

}
