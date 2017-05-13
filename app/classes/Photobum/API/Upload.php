<?php

namespace Photobum\API;

use Photobum\Utilities\General;
use Photobum\Utilities\S3\Put;

class Upload extends APIController
{

    public function get()
    {
        sd($this->f3->get('ROOT'));
        sd('get');
    }

    public function post()
    {        
        foreach($_FILES as $file) {

            if ($file['error']) {

                switch( $file['error'] ) {
                    case UPLOAD_ERR_OK:
                        $message = false;;
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $message = 'File too large (limit of '.ini_get("upload_max_filesize").' bytes).';
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $message = 'File upload was not completed.';
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $message = 'Zero-length file uploaded.';
                        break;
                    default:
                        $message = 'Internal error #'.$file['error'];
                        break;
                }
                General::flushJsonResponse([
                    'ack' => 'error',
                    'error_code'=> $file['error'],
                    'msg' => $message
                ]);
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newFilename = time().'-'.rand(1, 999999).'.'.$ext;

            $res = (new Put())->uploadAlbum($file['tmp_name'], $newFilename);

            General::flushJsonResponse(['ack'=>'ok', 'location'=> $res['UploadURL'], 'new_filename'=> $newFilename]);

        }
    }

    public function put()
    {
        General::flushJsonResponse(['ack' => 'error', 'msg' => 'Sorry this API isn\'t really RESTful'], 405);
    }

    public function delete()
    {
        General::flushJsonResponse(['ack'=>'error'], 405);
    }
}
