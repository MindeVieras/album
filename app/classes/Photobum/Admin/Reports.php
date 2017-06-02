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
            'timezone' => date_default_timezone_get(),
            'page' => $this->page,
            'user' => $this->user
        ]);
    }

    public function server_info()
    {
        $template = $this->twig->loadTemplate('Admin/Reports/server-info.html');
        echo $template->render([
            'page' => $this->page
        ]);
    }

    public function videos()
    {
        $template = $this->twig->loadTemplate('Admin/Reports/videos.html');
        echo $template->render([
            'page' => $this->page,
            'videos' => $this->getVideosStatus()
        ]);
    }

    public function getDirStatus(){
        $dirs = array(
            'cache',
            'uploads',
            'media',
            'media'.DS.'albums',
            'media'.DS.'persons',
            'media'.DS.'users',
            'media'.DS.'videos',
            'media'.DS.'videos'.DS.'small',
            'media'.DS.'videos'.DS.'medium',
            'media'.DS.'videos'.DS.'hd',
            'media'.DS.'videos'.DS.'fullhd'
        );
        
        // check directories
        foreach ($dirs as $d) {
            $upl_dir = array(
                'path' => DS.$d,
                'ack' => 'ok',
                'msg' => 'Directory is healthy.'
            );
            if(!is_dir(getcwd().DS.$d)){
                $upl_dir = array(
                    'path' => DS.$d,
                    'ack' => 'error',
                    'msg' => 'Directory doesn\'t exist.'
                );
            } elseif (!is_writable(getcwd().DS.$d)){
                $upl_dir = array(
                    'path' => DS.$d,
                    'ack' => 'warn',
                    'msg' => 'Dirctory not writable.'
                );
            }
            $data[] = $upl_dir;
        }

        return $data;
    }

    public function getVideosStatus(){

        $videos = $this->db->exec("SELECT * FROM media WHERE file_type = 'video'");
        return $videos;
    }
}