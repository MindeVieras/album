<?php

namespace Photobum\Admin;

use Photobum\Utilities\General;
use DB\SQL\Mapper;
use Photobum\Config;
use \DateTime;

use Photobum\Utilities\Aws\S3\Listfiles;
use Photobum\Utilities\Aws\S3\Move;
use Photobum\Utilities\Aws\S3\Delete;

class Awsconsole extends Admin{

    public function __construct(){
        parent::__construct();
        //$this->initOrm('albums');
        //$this->model->url = 'SELECT url FROM urls WHERE urls.type_id = albums.id AND urls.type = \'album\'';
        $this->twig->onReady('PhotobumAdmin.awsReady');
        $this->page['title']= 'AWS Console';
        $this->page['section']= 'aws';
        $this->page['body_class']= 'aws';
        //ddd($color);
    }

    public function view($params){
        $this->auth();
        //$this->twig->onReady('PhotobumAdmin.viewAws');
        //$this->years = $this->db->exec("SELECT DISTINCT(YEAR(start_date)) as year FROM albums ORDER BY start_date DESC");
        $template = $this->twig->loadTemplate('Admin/Aws/view.html');
        echo $template->render([
            'page' => $this->page,
            //'years' => $this->years,
            'user' => $this->user
        ]);
    }

    public function info($params){

        $this->auth();
        $template = $this->twig->loadTemplate('Admin/Aws/info.html');
        echo $template->render([
            'page' => $this->page,
            'user' => $this->user
        ]);
    }

    public function s3($params){

        $this->auth();
        //ddd($params);
        $date = $params['date'];
        $template = $this->twig->loadTemplate('Admin/Aws/s3.html');
        echo $template->render([
            'page' => $this->page,
            'uploads' => $this->getS3Files('uploads'),
            'user' => $this->user
        ]);
    }

    private function getS3Files($path){
        $list = (new Listfiles())->listObjects($path);
        foreach ($list['Contents'] as $l) {
            $d[] = $l['Key'];
        }
        return $d;

    }

    // public function edit($params){
    //     $this->auth();
    //     $id = $params['id'];
    //     $this->model->load(['id=?', $id]);
    //     $cid = $this->model->color;
    //     $this->color = $this->db->exec("SELECT code FROM colors WHERE id = $cid");
    //     $template = $this->twig->loadTemplate('Admin/Album/edit.html');
    //     echo $template->render([
    //         'locations' => $this->getLocations($params['id']),
    //         'media' => $this->getMedia($params['id'], 1000),
    //         'color' => $this->color[0]['code'],
    //         'colors' => General::getColors(),
    //         'persons' => General::getPersons($params['id']),
    //         'item' => $this->model->cast(),
    //         'page' => $this->page
    //     ]);
    // }

    // public function delete($params){
    //     $this->auth();
    //     if ($this->f3->get('VERB') == 'DELETE') {
    //         $id = $params['id'];
                            
    //         // remove album
    //         $this->db->exec("DELETE FROM albums WHERE id = '$id'");
    //         // remove url
    //         $this->db->exec("DELETE FROM urls WHERE type = 'album' AND type_id = '$id'");
    //         // remove media locations
    //         $this->db->exec("DELETE FROM locations WHERE album_id = '$id'");
    //         // remove media urls
    //         $this->db->exec("DELETE FROM media WHERE type_id = '$id'");
    //         // remove persons relations
    //         $this->db->exec("DELETE FROM persons_rel WHERE album_id = '$id'");

    //         General::flushJsonResponse(['ack' => 'ok']);
    //     }
    //     General::flushJsonResponse([ack=>'Error', 'msg'=>'Could not delete item']);
    // }

    // private function getAlbums($date){

    //     $albums = $this->db->exec("SELECT 
    //                                     albums.*,
    //                                     urls.url,
    //                                     colors.code
    //                                 FROM
    //                                     albums
    //                                     JOIN urls ON albums.id = urls.type_id AND urls.type = 'album'
    //                                     JOIN colors ON albums.color = colors.id
    //                                 WHERE YEAR(albums.start_date) = '$date'
    //                                 ORDER BY created DESC LIMIT 200");
    //     //ddd($albums);

    //     foreach ($albums as $a) {

    //         $date = new DateTime($a['start_date']);
    //         $date_path = $date->format('Y'.DS.'m'.DS.'d');
    //         $name = \Web::instance()->slug($a['name']);
    //         $cid = $a['color'];

    //         $d['id'] = $a['id'];
    //         $d['name'] = $a['name'];
    //         $d['date'] = $date->format('Y-m-d H:i:s');
    //         $d['media_dir'] = getcwd().DS.'media'.DS.'albums'.DS.$date_path.DS.$name;
    //         $d['url'] = $a['url'];
    //         $d['color'] = $a['code'];
    //         $d['created'] = $a['created'];
    //         $d['media'] = $this->getMedia($a['id'], 2);

    //         $data[] = $d;
    //     }

    //     return $data;
    // }

    // private function getMedia($id, $limit){

    //     $media = $this->db->exec("SELECT * from media WHERE type_id = '$id' ORDER BY weight ASC LIMIT $limit");
        
    //     foreach ($media as $m) {
    //         $medi['id'] = $m['id'];
    //         $medi['url'] = $m['file_url'];
    //         $medi['file_type'] = $m['file_type'];
    //         $medi['name'] = basename($m['file_url']);
    //         $medi['weight'] = $m['weight'];
    //         if(file_exists(getcwd().$m['file_url'])){            
    //             $file_size = filesize(getcwd().$m['file_url']);
    //             $medi['size'] = General::formatSizeUnits($file_size);
    //             if($m['file_type'] == 'image'){
    //                 $exif = @exif_read_data(getcwd().$m['file_url']);
    //                 if($exif['DateTimeOriginal']){
    //                     $ex_date = new DateTime($exif['DateTimeOriginal']);
    //                     $date = $ex_date->format('Y-m-d H:i:s');
    //                     $medi['date_taken'] = $date;
    //                 }
    //                 //$medi['camera'] = $exif['Make'].' ( '.substr($exif['Model'], 0, 10).' )';
    //                 //$medi['date_taken'] = $exif;
    //             }
    //         }


    //         $md[] = $medi;
    //     }
    //     return $md;
    // }

    // private function getLocations($id){
    //     $locations = $this->db->exec("SELECT id, lat, lng from locations WHERE album_id = '$id'");
    //     return $locations;
    // }

}