<?php

namespace Photobum\Admin;


use Photobum\Utilities\General;
use DB\SQL\Mapper;
use Photobum\Config;

class Persons extends Admin
{

    public function __construct()
    {
        parent::__construct();
        $this->initOrm('persons');
        $this->model->url = 'SELECT url FROM urls WHERE urls.type_id = persons.id AND urls.type = \'person\'';
        $this->twig->onReady('PhotobumAdmin.personsReady');
        $this->page['section']= 'persons';

    }

    public function view($params)
    {
        $this->auth();
        $this->twig->onReady('PhotobumAdmin.viewPersons');
        $this->results = $this->db->exec('SELECT 
                                                persons.*,
                                                urls.url
                                            FROM
                                                persons
                                                JOIN urls ON persons.id = urls.type_id AND urls.type = \'person\'
                                            ORDER BY created DESC LIMIT 10');
        $template = $this->twig->loadTemplate('Admin/Person/persons.html');
        echo $template->render([
            'page' => $this->page,
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
                General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Name is required']);
            }

            // check if exists
            //$isurl = General::makeUrl($item['name'], 'persons');

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

                // update url
                $url = General::makeUrl($this->model->person_name, 'persons');
                $urls = $this->initOrm('urls', true);
                $urls->load(['type_id=? and type=\'person\'', $item['id']]);
                $urls->url = $url['url'];
                $urls->save();

                if ($this->model->dry()) {
                    General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Couldn\'t edit this person']);
                }
            }

            $this->model->person_name = $item['name'];
            // $this->model->headline_image = $item['headline_image'];
            // $this->model->hilite_para = $item['hilite_para'];
            // $this->model->body = $item['body'];
            // $this->model->attribution_id = $item['attribution_id'];
            // $this->model->publish_date = $item['publish_date'];
            // $this->model->publish = intval($item['publish'] == 'true');
            $this->model->save();
            
            if (!$editMode) {
                // save urls
                $url = General::makeUrl($this->model->person_name, 'persons');
                $urls = $this->initOrm('urls', true);
                $urls->load(['id=?', $item['id']]);
                $urls->url = $url['url'];
                $urls->type = 'person';
                $urls->type_id = $this->model->id;
                $urls->save();
            }

            $data = ['ack' => 'OK'];
            General::flushJsonResponse($data);

        }else{
            $template = $this->twig->loadTemplate('Admin/Person/addperson.html');
            echo $template->render([
                'page' => $this->page
            ]);
        }
    }

    public function edit($params)
    {
        $this->auth();
        $this->model->load(['id=?', $params['id']]);
        $template = $this->twig->loadTemplate('Admin/Person/editperson.html');
        echo $template->render([
            'item' => $this->model->cast(),
            'page' => $this->page
        ]);
    }

    public function delete($params)
    {
        $this->auth();
        if ($this->f3->get('VERB') == 'DELETE') {
            $id = $params['id'];
            $this->model->load(['id=?', $id]);
            if(!$this->model->dry()){
                $this->model->erase();
                // also remove url
                $this->db->exec("DELETE FROM urls WHERE type = 'person' AND type_id = '$id'");

                General::flushJsonResponse([ack=>'OK']);
            }

        }
        General::flushJsonResponse([ack=>'Error', 'msg'=>'Could not delete item']);
    }

}