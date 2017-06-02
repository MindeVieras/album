<?php

namespace Photobum\API;

use Photobum\Utilities\General;
use Photobum\Config;

use Photobum\Utilities\Aws\S3\Delete;

class AwsAPI extends APIController{

    public function s3_deleteObject(){
        
        if ($this->f3->get('VERB') == 'POST') {

            $data = $this->f3->get('POST');
            $key = $data['url'];

            
            // Delete file from S3
            (new Delete())->deleteObject($key);

            General::flushJsonResponse([
                'ack' => 'ok',
                'msg' => 'Deleted!'
            ]);

        }
    }

}