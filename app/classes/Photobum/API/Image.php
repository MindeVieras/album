<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 07/09/16
 * Time: 16:28
 */

namespace Photobum\API;

use Photobum\Utilities\General;

class Image extends APIController
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

            $ds = DIRECTORY_SEPARATOR;
            $storeFolder = 'uploads';
            $tempFile = $file['tmp_name'];
            $base_path = getcwd();
            $targetPath = $base_path.$ds.$storeFolder.$ds;
             
            $targetFile =  $targetPath.$file['name'];
         
            move_uploaded_file($tempFile, $targetFile);

            
            // $t = '/private/var/tmp/1.jpg';

            //rename($tmp_name, '/Users/mindevieras/sites/album/images/image.jpg');

        }
        General::flushJsonResponse([
            'ack' => 'ok',
            'location' => $targetFile
        ]);
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
