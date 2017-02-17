<?php

namespace Photobum\Admin;


use Photobum\Utilities\General;
use DB\SQL\Mapper;
use Photobum\Config;
use \DateTime;

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
        $template = $this->twig->loadTemplate('Admin/Album/albums.html');
        echo $template->render([
            'page' => $this->page,
            //'artists' => $this->getArtists(),
            'data' => $this->getAlbums(),
            'user' => $this->user
        ]);
    }


    public function add(){
        $this->auth();
        if ($this->f3->get('VERB') == 'POST') {
            $item = $this->f3->get('POST');

            if (!$item['name'] ) {
                General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Album name is required']);
            }

            if (!$item['start_date'] ) {
                General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Album date is required']);
            }

            $ds = DIRECTORY_SEPARATOR;
            
            $date = new DateTime($item['start_date']);
            $year = $date->format('Y');
            $name = \Web::instance()->slug($item['name']);
            
            $this->model->reset();

            $editMode = $item['id'] ? true : false;

            if ($editMode) {
                $this->model->load(['id=?', $item['id']]);

                $old_name = \Web::instance()->slug($this->model->album_name);
                if($old_name != $name){
                    $oldname = getcwd().$ds.'media'.$ds.'albums'.$ds.$year.$ds.$old_name;
                    $newname = getcwd().$ds.'media'.$ds.'albums'.$ds.$year.$ds.$name;
                    //exec("mv \'.$oldname.\' \'.$newname.\'");
                    $this->rcopy($oldname, $newname);
                }
                
                $id = $item['id'];
                // update url
                $url = General::makeUrl($this->model->album_name, 'albums');
                $urls = $this->initOrm('urls', true);
                $urls->load(['type_id=? and type=\'album\'', $id]);
                $urls->url = $url['url'];
                $urls->save();
                if ($this->model->dry()) {
                    General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Couldn\'t edit this news item']);
                }

                // remove locations before it gats saved
                $this->db->exec("DELETE FROM locations WHERE album_id = '$id'");
                // remove persons relations before save
                $this->db->exec("DELETE FROM persons_rel WHERE album_id = '$id'");
            }

            $this->model->album_name = $item['name'];
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
            
            if (!$editMode) {

                // save image urls
                if(!empty($item['album_images'])){
                    foreach ($item['album_images'] as $val) {
                        $urls = $this->initOrm('media', true);
                        //$urls->load(['id=?', $item['id']]);
                        $urls->file_url = $ds.'media'.$ds.'albums'.$ds.$year.$ds.$name.$ds.basename($val['value']);
                        $urls->album_id = $this->model->id;
                        $urls->save();

                        $res_urls[] = $val['value'];
                    }
                }
                // save urls
                $url = General::makeUrl($this->model->album_name, 'albums');
                $urls = $this->initOrm('urls', true);
                $urls->url = $url['url'];
                $urls->type = 'album';
                $urls->type_id = $this->model->id;
                $urls->save();
            }


            $data = ['ack' => 'ok', 'res' => $item['locations']];
            General::flushJsonResponse($data);

        }else{
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
            'media' => $this->getMedia($params['id']),
            'persons' => $this->getPersons($params['id']),
            'item' => $this->model->cast(),
            'page' => $this->page
        ]);
    }

    public function delete($params){
        $this->auth();
        if ($this->f3->get('VERB') == 'DELETE') {
            $id = $params['id'];
            $this->model->load(['id=?', $id]);
            
            if(!$this->model->dry()){
                
                $this->model->erase();
                
                // remove url
                $this->db->exec("DELETE FROM urls WHERE type = 'album' AND type_id = '$id'");
                // remove media locations
                $this->db->exec("DELETE FROM locations WHERE album_id = '$id'");
                // remove media urls
                $this->db->exec("DELETE FROM media WHERE album_id = '$id'");
                // remove persons relations
                $this->db->exec("DELETE FROM persons_rel WHERE album_id = '$id'");

                General::flushJsonResponse([ack=>'OK']);
            }
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
            $slug = \Web::instance()->slug($a['album_name']);

            $d['id'] = $a['id'];
            $d['name'] = $a['album_name'];
            $d['media_dir'] = getcwd().$ds.'media'.$ds.'albums'.$ds.$year.$ds.$slug;
            $d['url'] = $a['url'];
            $d['created'] = $a['created'];
            $d['media'] = $this->getMedia($a['id']);

            $data[] = $d;
        }

        return $data;
    }

    private function getMedia($id){

        $media = $this->db->exec("SELECT file_url from media WHERE album_id = '$id'");
        
        $i = 0;
        foreach ($media as $m) {
            $medi[] = $m['file_url'];
            if (++$i == 3) break;
        }
        return $medi;
    }

    private function getLocations($id){
        $locations = $this->db->exec("SELECT id, lat, lng from locations WHERE album_id = '$id'");
        return $locations;
    }
   // Function to remove folders and files 
    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file)
                if ($file != "." && $file != "..") rrmdir("$dir/$file");
            rmdir($dir);
        }
        else if (file_exists($dir)) unlink($dir);
    }

    // Function to Copy folders and files       
    private function rcopy($src, $dst) {
        if (file_exists ( $dst ))
            rrmdir ( $dst );
        if (is_dir ( $src )) {
            mkdir ( $dst );
            $files = scandir ( $src );
            foreach ( $files as $file )
                if ($file != "." && $file != "..")
                    rcopy ( "$src/$file", "$dst/$file" );
        } else if (file_exists ( $src ))
            copy ( $src, $dst );
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