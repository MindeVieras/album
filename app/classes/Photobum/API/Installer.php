<?php

namespace Photobum\API;

use Photobum\Base;
use Photobum\Config;
use Photobum\Utilities\General;

class Installer extends APIController{

    public function composerCheckStatus(){
        
        if ($this->f3->get('VERB') == 'POST') {

            $data = $this->f3->get('POST');

            if($data['function'] == 'getJson'){
                $output = array(
                    'composerJson' => file_exists(Config::get('BASE_PATH').'composer.json')
                );
                $ack = 'ok';
            }

            if($data['function'] == 'getPackages'){
                $output = array(
                    'composerPackages' => $this->getPackages(Config::get('BASE_PATH').'composer.json')
                );
                $ack = 'ok';
            }

        }
        General::flushJsonResponse([
            'ack' => $ack,
            'msg' => $output
        ]);
    }

    public function getPackages($path){
        $string = file_get_contents($path);
        $json = json_decode($string, false);

        foreach ($json->require as $k => $j) {
            //$d[] = $j->require;

            $required[] = $k;
        }
        // foreach ($json->require-dev as $k => $j) {
        //     //$d[] = $j->require;

        //     $required-dev[] = $k;
        // }

        return $required;
        //sd($json->require);
    }

}