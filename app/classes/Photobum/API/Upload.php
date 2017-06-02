<?php

namespace Photobum\API;

use Photobum\Utilities\General;
use Photobum\Utilities\Aws\S3\Put;
use Photobum\Utilities\Aws\Transcoder\Job;

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
            $rand_filename = time().'-'.rand(1, 999999);
            $newFilename = $rand_filename.'.'.$ext;
            $bucketDest = 'uploads/'.$newFilename;
            $ec2Dest = getcwd().'/uploads/'.$newFilename;

            $vthumb = explode('.', $newFilename);
            $videoThumbName = $vthumb[0];
            //General::flushJsonResponse(['ack'=>'ok', 'msg'=> $file]);

            if(move_uploaded_file($file['tmp_name'], $ec2Dest)){

                // Send original file to S3
                $res = (new Put())->uploadAlbum($ec2Dest, $bucketDest);

                if($file['type'] == 'image/jpeg'){
                    // Make temporary local EC2 UHD image to make other thumbs for less memory
                    exec("convert ".$ec2Dest." -thumbnail '3840x2160>' ".$ec2Dest);
                    //$uhdImg = General::generateThumb($ec2Dest, $ec2Dest, 3840, 2160, false);
                    $uhdUrl = getcwd().'/uploads/styles/uhd/'.$newFilename;
                    rename($ec2Dest, $uhdUrl);

                    // Generate styles and send to S3
                    foreach ($this->img_styles as $style) {
                        //$crop = ($style['crop'] == 1) ? true : false;

                        $styleFilePath = 'uploads/styles/'.$style['name'].'/'.$newFilename;
                        $ec2FilePath = getcwd().'/'.$styleFilePath;

                        if ($style['crop'] == 1) {
                            $command = sprintf('convert %s -resize \'%dx%d^\' -gravity center -extent %dx%d %s', $uhdUrl, $style['width'], $style['height'], $style['width'], $style['height'], $ec2FilePath);
                        } else {
                            $command = sprintf('convert %s -thumbnail \'%dx%d>\' %s', $uhdUrl, $style['width'], $style['height'], $ec2FilePath);
                        }

                        // Make temporary local thumb
                        exec($command);
                        //$thumbImg = General::generateThumb($uhdUrl, $ec2FilePath, $style['width'], $style['height'], $crop);

                        (new Put())->uploadAlbum($ec2FilePath, $styleFilePath);
                        if(file_exists($ec2FilePath)){
                            unlink($ec2FilePath);
                        }
                    }

                    General::flushJsonResponse(['ack'=>'ok', 'location'=> $res['UploadURL'], 'new_filename'=> $newFilename, 'msg'=> $file]);
                }

                if($file['type'] == 'video/mp4'){

                    $src = $res['UploadURL'];

                    foreach ($this->video_sizes as $size) {
                        $dest = 'uploads/videos/'.$size.'/'.$newFilename;
                        $video = (new Job())->createJob($src, $dest, $size, $videoThumbName);
                    }            
                    if(file_exists($ec2Dest)){
                        unlink($ec2Dest);
                    }
                    General::flushJsonResponse(['ack'=>'ok', 'location'=> $res['UploadURL'], 'new_filename'=> $newFilename, 'msg'=> $ec2Dest]);
                }

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
