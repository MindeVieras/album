<?php

namespace Photobum\Admin;


use Photobum\Utilities\General;
use DB\SQL\Mapper;
use Photobum\Config;

class Colors extends Admin{

    public function __construct(){
        parent::__construct();
        $this->initOrm('albums');
        $this->twig->onReady('PhotobumAdmin.colorsReady');
        $this->page['title']= 'Manage Colors';
        $this->page['body_class']= 'colors';
        $this->page['section']= 'colors';
    }

    public function view($params){
        $this->auth();
        $this->twig->onReady('PhotobumAdmin.viewColors');
        $template = $this->twig->loadTemplate('Admin/Colors/view.html');
        echo $template->render([
            'page' => $this->page,
            'data' => 'no datakk',
            'albums_colors' => General::getColors('album'),
            'persons_colors' => General::getColors('person'),
            'user' => $this->user
        ]);
        //ddd($this);
    }

    public function add(){
        $this->auth();
        if ($this->f3->get('VERB') == 'POST') {
            $item = $this->f3->get('POST');

            $color = $item['color'];
            $type = $item['type'];

            $c = $this->initOrm('colors', true);            
            $c->code = $color;
            $c->type = $type;
            $c->save();

            $id = $c->id;

            $data = ['ack' => 'ok', 'id' => $id, 'msg' => $color];
            General::flushJsonResponse($data);

        }
    }

    public function save(){
        $this->auth();
        if ($this->f3->get('VERB') == 'POST') {
            $item = $this->f3->get('POST');


            // save locations
            if (!empty($item['colors'])){
                
                $c = $this->initOrm('colors', true);
                foreach ($item['colors'] as $color) {
                    $c->load(['id=?', $color['id']]);
                    $c->code = $color['code'];
                    $c->save();
                }
            }

            $data = ['ack' => 'ok', 'msg' => $item];
            General::flushJsonResponse($data);

        }
    }

}