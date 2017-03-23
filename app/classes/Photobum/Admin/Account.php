<?php

namespace Photobum\Admin;


use Photobum\Utilities\General;
use DB\SQL\Mapper;
use Photobum\Config;

class Account extends Admin
{

    public function __construct()
    {
        parent::__construct();
        $this->initOrm('settings');
        $this->twig->onReady('PhotobumAdmin.settingsReady');
        $this->page['title']= $this->page['user']['display_name'].'\'s account';
        $this->page['body_class']= 'account';
        $this->page['section']= 'account';
        //ddd($this->page['user']['id']);

    }

    public function view($params)
    {
        $this->auth();
        $this->twig->onReady('PhotobumAdmin.viewSettings');
        $this->results = $this->db->exec('SELECT 
                                                persons.*,
                                                urls.url
                                            FROM
                                                persons
                                                JOIN urls ON persons.id = urls.type_id AND urls.type = \'person\'
                                            ORDER BY created DESC LIMIT 10');
        $template = $this->twig->loadTemplate('Admin/Account/view.html');
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
            $template = $this->twig->loadTemplate('Admin/Person/add.html');
            echo $template->render([
                'page' => $this->page
            ]);
        }
    }


}