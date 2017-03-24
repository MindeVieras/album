<?php

namespace Photobum\API;

use Photobum\Utilities\General;

class Utilities extends APIController{

    public function generateSlug()
    {
        //         $f3 = \Base::instance();
        // $web = \Web::instance();
        // $slug = $web->slug($title);
        // if ($count == 0) {
        //     $url = sprintf('/%s/%s', $web->slug($area), $slug);
        // } else {
        //     $url = sprintf('/%s/%s-%d', $web->slug($area), $slug, $count);
        // }
        // $model = new Mapper($f3->get('DB'), 'urls');
        // $model->load(['url=?', $url]);
        // if ($model->dry()) {
        //     return ['url' => $url, 'slug' => $slug];
        // } else {
        //     return self::makeUrl($title, $area, ++$count);
        // }
        General::flushJsonResponse( General::makeUrl(
            $this->f3->get('REQUEST.value')
        ));
    }

    public function collapseMenu(){
        
        if ($this->f3->get('VERB') == 'POST') {
            
            $data = $this->f3->get('POST');

            $men = $this->initOrm('user_settings', true);   
            $men->load(['user_id=?', $data['id']]);
            if(!$men->dry()){
                $men->menu_collapsed = $data['status'];
                $men->save();
            }

        }
    }

    public function deleteAlbumDir(){
        
        if ($this->f3->get('VERB') == 'POST') {
            $ack = 'ok';
            $data = $this->f3->get('POST');

            //$this->rrmdir($data['dir']);
            $command = 'rm -Rf '.$data['dir'];
            shell_exec($command);

        }
        General::flushJsonResponse([
            'ack' => $ack,
            'msg' => $data
        ]);
    }

}