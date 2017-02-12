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
        	'dir_status' => $this->getDirStatus(),
            'page' => $this->page,
            'user' => $this->user
        ]);
    }

    public function getDirStatus(){
   		$ds = DIRECTORY_SEPARATOR;
        $dirs = array('uploads', 'media', 'media'.$ds.'albums', 'media'.$ds.'styles');
        
        // check directories
        foreach ($dirs as $d) {
            
            $upl_dir = array(
                'name' => $d,
                'ack' => 'ok',
                'status' => 'All is good.'
            );
            if(!is_dir(getcwd().$ds.$d)){
                $upl_dir = array(
                    'name' => $d,
                    'ack' => 'error',
                    'status' => 'Doesn\'t exist.'
                );
            } elseif (!is_writable(getcwd().$ds.$d)){
                $upl_dir = array(
                    'name' => $d,
                    'ack' => 'error',
                    'status' => 'Not writable.'
                );
            }
            $data[] = $upl_dir;
        }

    	return $data;
    }
}