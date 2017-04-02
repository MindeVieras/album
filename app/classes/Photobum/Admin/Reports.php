<?php

namespace Photobum\Admin;


use Photobum\Utilities\General;
use DB\SQL\Mapper;
use Photobum\Config;

class Reports extends Admin{

    public function __construct(){
        parent::__construct();
        $this->page['title']= 'Reports';
        $this->page['body_class']= 'reports';
        $this->page['section']= 'reports';
    }

    public function view($params){
        $this->auth();
        $template = $this->twig->loadTemplate('Admin/Reports/reports.html');
        echo $template->render([
        	'dir_status' => $this->getDirStatus(),
            'page' => $this->page,
            'user' => $this->user
        ]);
    }

    public function getDirStatus(){
   		$ds = DIRECTORY_SEPARATOR;
        $dirs = array('uploads', 'media', 'media'.$ds.'albums', 'media'.$ds.'persons', 'media'.$ds.'users');
        
        // check directories
        foreach ($dirs as $d) {
            
            $upl_dir = array(
                'path' => $d,
                'ack' => 'ok',
                'msg' => 'Directory is healthy.'
            );
            if(!is_dir(getcwd().$ds.$d)){
                $upl_dir = array(
                    'path' => $d,
                    'ack' => 'error',
                    'msg' => 'Directory doesn\'t exist.'
                );
            } elseif (!is_writable(getcwd().$ds.$d)){
                $upl_dir = array(
                    'path' => $d,
                    'ack' => 'warn',
                    'msg' => 'Dirctory not writable.'
                );
            }
            $data[] = $upl_dir;
        }

    	return $data;
    }
}