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
        $styles = $this->db->exec("SELECT * FROM media_styles ORDER BY id ASC");

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
            $bucketDest = 'uploads/'.$newFilename;
            $ec2Dest = getcwd().'/uploads/'.$newFilename;

            if(move_uploaded_file($file['tmp_name'], $ec2Dest)){

                // Sent original file to S3
                $res = (new Put())->uploadAlbum($ec2Dest, $bucketDest);

                // Make temporary local EC2 UHD image to make other thumbs for less memory
                $uhdImg = General::generateThumb($ec2Dest, $ec2Dest, 3840, 2160, false);
                $uhdUrl = getcwd().'/uploads/styles/uhd/'.$newFilename;
                rename($uhdImg, $uhdUrl);

                // Generate styles and send to S3
                foreach ($styles as $style) {
                    $crop = ($style['crop'] == 1) ? true : false;
                    $styleFilePath = 'uploads/styles/'.$style['name'].'/'.$newFilename;
                    $ec2FilePath = getcwd().'/'.$styleFilePath;
                    
                    // Make temporary local thumb
                    $thumbImg = General::generateThumb($uhdUrl, $ec2FilePath, $style['width'], $style['height'], $crop);

                    (new Put())->uploadAlbum($thumbImg, $styleFilePath);
                    if(file_exists($thumbImg)){
                        unlink($thumbImg);
                    }

                }

                General::flushJsonResponse(['ack'=>'ok', 'location'=> $res['UploadURL'], 'new_filename'=> $newFilename, 'tmp_uhd'=> $thumbImg]);
            }

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
