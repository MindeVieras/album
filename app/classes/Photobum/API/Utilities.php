<?php

namespace Photobum\API;

use Photobum\Utilities\General;
use Photobum\Config;

class Utilities extends APIController{

    public function generateThumb()
    {
                
        if ($this->f3->get('VERB') == 'POST') {
            
            $data = $this->f3->get('POST');
            $url = $data['url'];
            $width = $data['width'];
            $height = $data['height'];
            $fit = $data['fit'];

            if(!file_exists(getcwd().$url)){
                General::flushJsonResponse(['ack'=>'ok', 'url'=>'/error.png']);
            }

            $name = basename($url);
            $style_path = DS.'cache'.DS.'styles'.DS.$width.'x'.$height;

            // Create style dir
            if(!file_exists(getcwd().$style_path)){
                mkdir(getcwd().$style_path, 0777, true);
            }

            $cache_file = $style_path.DS.$name;

            $img_src = getcwd().$url;
            $img_dest = getcwd().$cache_file;

            list($source_image_width, $source_image_height, $source_image_type) = getimagesize($img_src);
            switch ($source_image_type) {
                case IMAGETYPE_GIF:
                    $source_gd_image = imagecreatefromgif($img_src);
                    break;
                case IMAGETYPE_JPEG:
                    $source_gd_image = imagecreatefromjpeg($img_src);
                    break;
                case IMAGETYPE_PNG:
                    $source_gd_image = imagecreatefrompng($img_src);
                    break;
            }

            if ($source_gd_image === false) {
                General::flushJsonResponse(['ack'=>'error', 'url'=>$url, 'msg'=>'Cant use GD with this format.']);
            }

            $source_aspect_ratio = $source_image_width / $source_image_height;
            $thumbnail_aspect_ratio = $width / $height;
            if($fit == 'no'){
            
                if ($source_image_width <= $width && $source_image_height <= $height) {
                    $thumbnail_image_width = $source_image_width;
                    $thumbnail_image_height = $source_image_height;
                } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
                    $thumbnail_image_width = (int) ($height * $source_aspect_ratio);
                    $thumbnail_image_height = $height;
                } else {
                    $thumbnail_image_width = $width;
                    $thumbnail_image_height = (int) ($width / $source_aspect_ratio);
                }

                $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
                imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);

                imagejpeg($thumbnail_gd_image, $img_dest, 85);
                imagedestroy($thumbnail_gd_image);
            } else {
            
                if ($source_aspect_ratio > $thumbnail_aspect_ratio) {
                    $thumbnail_image_width = (int) ($height * $source_aspect_ratio);
                    $thumbnail_image_height = $height;
                } else {
                    $thumbnail_image_width = $width;
                    $thumbnail_image_height = (int) ($width / $source_aspect_ratio);
                }

                $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
                imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);


                $img_fit = imagecreatetruecolor($width, $height);

                imagecopy(
                    $img_fit,
                    $thumbnail_gd_image,
                    0,
                    0,
                    ($thumbnail_image_width - $width) / 2,
                    ($thumbnail_image_height - $height) / 2,
                    $width,
                    $height
                );

                imagejpeg($img_fit, $img_dest, 85);
                imagedestroy($img_fit);
            }

            imagedestroy($source_gd_image);

            General::flushJsonResponse(['ack'=>'ok', 'url'=>$cache_file]);
        }
    }

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

    public function fixDir(){
        
        if ($this->f3->get('VERB') == 'POST') {
            $ack = 'ok';
            $data = $this->f3->get('POST');
            $dir = $data['dir'];

            $path = rtrim(Config::get('BASE_PATH'), DS).$dir;
            
            // make DIR
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            } else {
                exec('chmod -Rf 777 '.$path);
            }
            
            General::flushJsonResponse([
                'ack' => $ack,
                'msg' => $path
            ]);

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