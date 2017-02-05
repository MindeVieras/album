<?php

namespace Music\Admin;


use Music\Utilities\General;
use DB\SQL\Mapper;
use Music\Config;

class Albums extends Admin
{

    public function __construct()
    {
        parent::__construct();
        $this->initOrm('albums');
        $this->model->url = 'SELECT url FROM urls WHERE urls.type_id = albums.id AND urls.type = \'album\'';
        $this->twig->onReady('MusicAdmin.albumsReady');
        $this->page['section']= 'albums';

    }

    public function view($params)
    {
        $this->auth();
        $this->twig->onReady('MusicAdmin.viewAlbums');
        $this->results = $this->db->exec('SELECT 
                                                albums.*,
                                                artists.artist_name,
                                                urls.url
                                            FROM
                                                albums
                                                JOIN urls ON albums.id = urls.type_id AND urls.type = \'album\'
                                                JOIN artists ON artists.id = albums.artist_id
                                                
                                            ORDER BY created DESC LIMIT 10');
        $template = $this->twig->loadTemplate('Admin/albums.html');
        echo $template->render([
            'page' => $this->page,
            'artists' => $this->getArtists(),
            'data' => $this->results,
            'user' => $this->user
        ]);
    }


    public function add()
    {
        $this->auth();
        if ($this->f3->get('VERB') == 'POST') {
            $item = $this->f3->get('POST');

            if (!$item['name'] ) {
                General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Headline is required']);
            }
            // if (!$item['body'] ) {
            //     General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Body is required']);
            // }
            // if (!$item['headline_image'] ) {
            //     General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Headline image is required']);
            // }

            $this->model->reset();


            $editMode = $item['id'] ? true : false;

            if ($editMode) {
                $this->model->load(['id=?', $item['id']]);
                if ($this->model->dry()) {
                    General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Couldn\'t edit this news item']);
                }
            }

            $this->model->album_name = $item['name'];
            $this->model->artist_id = $item['artist_id'];
            // $this->model->headline_image = $item['headline_image'];
            // $this->model->hilite_para = $item['hilite_para'];
            // $this->model->body = $item['body'];
            // $this->model->publish_date = $item['publish_date'];
            // $this->model->publish = intval($item['publish'] == 'true');
            $this->model->save();
            if ($this->model->id && !$editMode ) {
                $url = General::makeUrl($this->model->album_name, 'albums');
                $urls = $this->initOrm('urls', true);
                $urls->url = $url['url'];
                $urls->type = 'album';
                $urls->type_id = $this->model->id;
                $urls->save();
            }
            $data = ['ack' => 'OK'];
            General::flushJsonResponse($data);

        }else{
            $template = $this->twig->loadTemplate('Admin/addalbum.html');
            echo $template->render([
                'artists' => $this->getArtists(),
                'page' => $this->page
            ]);
        }
    }

    public function edit($params)
    {
        $this->auth();
        $this->model->load(['id=?', $params['id']]);
        $template = $this->twig->loadTemplate('Admin/editalbum.html');
        echo $template->render([
            'artists' => $this->getArtists(),
            'item' => $this->model->cast(),
            'page' => $this->page
        ]);
    }

    public function delete($params)
    {
        $this->auth();
        if ($this->f3->get('VERB') == 'DELETE') {
            $this->model->load(['id=?', $params['id']]);
            if(!$this->model->dry()){
                $this->model->erase();
                General::flushJsonResponse([ack=>'OK']);
            }

        }
        General::flushJsonResponse([ack=>'Error', 'msg'=>'Could not delete item']);
    }

    private function getArtists($personalise = false)
    {
        $authors = $this->db->exec('select id, artist_name from artists WHERE artist_name != \'\' ORDER by artist_name ');
        if ($personalise) {
            $me = $this->page['user']['id'];
            foreach($authors as $k => $v) {
                if ($v['id'] == $me) {
                    $authors[$k]['name'] = sprintf('%s (YOU)', $v['attribution_name']);
                    return $authors;
                }
            }
        }
        return $authors;
        //return 'artistsss goes herere';
    }


}