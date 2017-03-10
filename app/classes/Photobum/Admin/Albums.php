<?php

namespace Photobum\Admin;


use Photobum\Utilities\General;
use DB\SQL\Mapper;
use Photobum\Config;
use \DateTime;

use Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class Albums extends Admin{

    public function __construct(){
        parent::__construct();
        $this->initOrm('albums');
        $this->model->url = 'SELECT url FROM urls WHERE urls.type_id = albums.id AND urls.type = \'album\'';
        $this->twig->onReady('PhotobumAdmin.albumsReady');
        $this->page['section']= 'albums';
    }

    public function view($params){
        $this->auth();
        $this->twig->onReady('PhotobumAdmin.viewAlbums');
        $template = $this->twig->loadTemplate('Admin/Album/view.html');
        echo $template->render([
            'page' => $this->page,
            'data' => $this->getAlbums(),
            'user' => $this->user
        ]);
    }

    public function add(){
        $this->auth();
        if ($this->f3->get('VERB') == 'POST') {
            $item = $this->f3->get('POST');

            if(!$item['name']){General::flushJsonResponse(['ack'=>'error', 'msg'=>'Album name is required']);}

            if(!$item['start_date']){General::flushJsonResponse(['ack'=>'error', 'msg'=>'Album date is required']);}

            $this->model->reset();

            $ds = DIRECTORY_SEPARATOR;
            
            $date = new DateTime($item['start_date']);
            $year = $date->format('Y');
            $name = \Web::instance()->slug($item['name']);

            $file_path = getcwd().$ds.'media'.$ds.'albums'.$ds.$year.$ds.$name;
            $file_path_style = getcwd().$ds.'media'.$ds.'albums'.$ds.$year.$ds.$name.$ds.'styles';
            $media_path = $ds.'media'.$ds.'albums'.$ds.$year.$ds.$name;
            $styles = $this->db->exec("SELECT * FROM media_styles");
            
            $editMode = $item['id'] ? true : false;

            if ($editMode) {

                $id = $item['id'];

                $this->model->load(['id=?', $id]);

                $old_name = \Web::instance()->slug($this->model->name);
                $new_name = \Web::instance()->slug($item['name']);

                if($old_name != $name){
                    $nameChanged = true;
                } else {
                    $nameChanged = false;
                }

                //sd($old_name, $new_name, $nameChanged);

                // update url
                $url = General::makeAlbumUrl($item['name'], $item['start_date']);
                $urls = $this->initOrm('urls', true);
                $urls->load(['type_id=? and type=\'album\'', $id]);
                $urls->url = $url['url'];
                $urls->save();

                // remove locations before it gats saved
                $this->db->exec("DELETE FROM locations WHERE album_id = '$id'");
                // remove persons relations before save
                $this->db->exec("DELETE FROM persons_rel WHERE album_id = '$id'");
                
                if(!empty($item['album_images_db'])){
                    
                    // get db media files
                    $db_files_urls = $this->db->exec("SELECT * FROM media WHERE album_id = $id");
                    
                    $db_urls = array_map(function($row){return $row['file_url'];}, $db_files_urls);
                    $form_urls = array_map(function($row){
                        $url = substr($row['value'], strlen(getcwd()));
                        return $url;
                    }, $item['album_images_db']);

                    $diff = array_values(array_diff($db_urls, $form_urls));

                    //sd($db_urls, $form_urls, $diff);
                    if(!empty($diff)) {
                        // remove media
                        foreach ($diff as $d) {
                            $this->db->exec("DELETE FROM media WHERE file_url = '$d'");
                            $full_path = getcwd().$d;
                            unlink($full_path);
                            foreach ($styles as $style) {
                                $path = $file_path_style.$ds.$style['name'].$ds.basename($full_path);
                                unlink($path);
                            }
                        }
                    }
                    
                }
            }

            $this->model->name = $item['name'];
            $this->model->start_date = $item['start_date'];
            $this->model->end_date = $item['end_date'];
            $this->model->location_name = $item['location_name'];
            $this->model->body = $item['body'];
            $this->model->private = intval($item['private'] == 'true');
            $this->model->save();

            // save locations
            if (!empty($item['locations'])){
                foreach ($item['locations'] as $loc) {
                    $latLng = explode(',', $loc['value']);
                    $locs = $this->initOrm('locations', true);            
                    $locs->lat = $latLng[0];
                    $locs->lng = $latLng[1];
                    $locs->album_id = $this->model->id;
                    $locs->save();
                }
            }
            // save persons
            if (!empty($item['album_persons'])){
                foreach ($item['album_persons'] as $per) {
                    $pers = $this->initOrm('persons_rel', true);            
                    $pers->person_id = $per['value'];
                    $pers->album_id = $this->model->id;
                    $pers->save();
                }
            }

            // rename files to dir and save media urls
            if(!empty($item['album_images'])){
                
                $imagine = new Imagine\Gd\Imagine();
                
                    //$id = $data['id'];
                
                // make media DIR
                if (!file_exists($file_path)) {
                    mkdir($file_path, 0777, true);
                }
                // make image styles DIR
                foreach ($styles as $style) {
                    if (!file_exists($file_path_style.$ds.$style['name'])) {
                        mkdir($file_path_style.$ds.$style['name'], 0777, true);
                    }
                }

                // rename files
                foreach ($item['album_images'] as $file) {
            
                    $tmp_file = $file['value'];
                    $file_name = basename($tmp_file);

                    // make image styles
                    foreach ($styles as $style) {

                        $image = $imagine->open($tmp_file);

                        $image->thumbnail(new Box($style['width'], $style['height']))
                            ->save($file_path_style.$ds.$style['name'].$ds.$file_name);
                    }

                    // move files
                    rename($tmp_file, $file_path.$ds.$file_name);

                    // save media urls
                    $urls = $this->initOrm('media', true);
                    $urls->file_url = $media_path.$ds.basename($tmp_file);
                    $urls->album_id = $this->model->id;
                    $urls->save();

                }
            }

            if (!$editMode) {
                // save url
                $url = General::makeAlbumUrl($item['name'], $item['start_date']);
                $urls = $this->initOrm('urls', true);
                $urls->url = $url['url'];
                $urls->type = 'album';
                $urls->type_id = $this->model->id;
                $urls->save();
            }

            $data = ['ack' => 'ok', 'msg' => $item['album_images_db']];
            General::flushJsonResponse($data);

        } else {
            $template = $this->twig->loadTemplate('Admin/Album/addalbum.html');
            echo $template->render([
                'persons' => $this->getPersons(0),
                'page' => $this->page
            ]);
        }
    }

    public function edit($params){
        $this->auth();
        $this->model->load(['id=?', $params['id']]);
        $template = $this->twig->loadTemplate('Admin/Album/editalbum.html');
        echo $template->render([
            'locations' => $this->getLocations($params['id']),
            'media' => $this->getMedia($params['id'], 1000),
            'persons' => $this->getPersons($params['id']),
            'item' => $this->model->cast(),
            'page' => $this->page
        ]);
    }

    public function delete($params){
        $this->auth();
        if ($this->f3->get('VERB') == 'DELETE') {
            $id = $params['id'];
                            
            // remove album
            $this->db->exec("DELETE FROM albums WHERE id = '$id'");
            // remove url
            $this->db->exec("DELETE FROM urls WHERE type = 'album' AND type_id = '$id'");
            // remove media locations
            $this->db->exec("DELETE FROM locations WHERE album_id = '$id'");
            // remove media urls
            $this->db->exec("DELETE FROM media WHERE album_id = '$id'");
            // remove persons relations
            $this->db->exec("DELETE FROM persons_rel WHERE album_id = '$id'");

            General::flushJsonResponse(['ack' => 'ok']);
        }
        General::flushJsonResponse([ack=>'Error', 'msg'=>'Could not delete item']);
    }

    private function getAlbums(){

        $albums = $this->db->exec('SELECT 
                                        albums.*,
                                        urls.url
                                    FROM
                                        albums
                                        JOIN urls ON albums.id = urls.type_id AND urls.type = \'album\'
                                    ORDER BY created DESC LIMIT 10');

        foreach ($albums as $a) {

            $ds = DIRECTORY_SEPARATOR;
            $date = new DateTime($a['start_date']);
            $year = $date->format('Y');
            $slug = \Web::instance()->slug($a['name']);

            $d['id'] = $a['id'];
            $d['name'] = $a['name'];
            $d['date'] = $date->format('Y-m-d H:i:s');
            $d['media_dir'] = getcwd().$ds.'media'.$ds.'albums'.$ds.$year.$ds.$slug;
            $d['url'] = $a['url'];
            $d['created'] = $a['created'];
            $d['media'] = $this->getMedia($a['id'], 3);

            $data[] = $d;
        }

        return $data;
    }

    private function getMedia($id, $limit){

        $media = $this->db->exec("SELECT file_url from media WHERE album_id = '$id' LIMIT $limit");
        
        foreach ($media as $m) {
            $medi['url'] = $m['file_url'];
            $medi['name'] = basename($m['file_url']);
            if(file_exists(getcwd().$m['file_url'])){            
                $file_size = filesize(getcwd().$m['file_url']);
                $medi['size'] = General::formatSizeUnits($file_size);

                $exif = exif_read_data(getcwd().$m['file_url']);
                if($exif['DateTimeOriginal']){
                    $ex_date = new DateTime($exif['DateTimeOriginal']);
                    $date = $ex_date->format('Y-m-d H:i:s');
                    $medi['date_taken'] = $date;
                }
                $medi['camera'] = $exif['Make'].' ( '.substr($exif['Model'], 0, 10).' )';
                //$medi['date_taken'] = $exif;
            }


            $md[] = $medi;
        }
        return $md;
    }

    private function getLocations($id){
        $locations = $this->db->exec("SELECT id, lat, lng from locations WHERE album_id = '$id'");
        return $locations;
    }

    private function getPersons($id){
        $persons = $this->db->exec("SELECT id, person_name FROM persons ORDER BY person_name");
        if ($id == 0){
            return $persons;
        } else {
            foreach ($persons as $p) {
                $d['person_id'] = $p['id'];
                $d['person_name'] = $p['person_name'];
                $d['checked'] = $this->personChecked($id, $p['id']);
                $data[] = $d;
            }
            return $data;
        }
    }

    private function personChecked($album_id, $person_id){
        
        $persons = $this->db->exec("SELECT id FROM persons_rel WHERE album_id = '$album_id' AND person_id = '$person_id'");
        if(!empty($persons)){
            $is_chcked = 1;
        } else {
            $is_chcked = 0;
        }
        return $is_chcked;
    }

}