<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 30/08/16
 * Time: 15:18
 */

namespace Photobum\Utilities;

use \DateTime;

class General
{
    

    public static function generateThumb($src, $dest, $width, $height, $crop = false)
    {

        if(!file_exists($src)){
            return '/error.png';
        }

        list($source_image_width, $source_image_height, $source_image_type) = getimagesize($src);
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $source_gd_image = imagecreatefromgif($src);
                break;
            case IMAGETYPE_JPEG:
                $source_gd_image = imagecreatefromjpeg($src);
                break;
            case IMAGETYPE_PNG:
                $source_gd_image = imagecreatefrompng($src);
                break;
        }

        if ($source_gd_image === false) {
            return '/error.png';
        }

        $source_aspect_ratio = $source_image_width / $source_image_height;
        $thumbnail_aspect_ratio = $width / $height;

        if($crop === false){
            //return $source_aspect_ratio.' + '.$thumbnail_aspect_ratio;
        
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

            imagejpeg($thumbnail_gd_image, $dest, 100);
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

            imagejpeg($img_fit, $dest, 100);
            imagedestroy($thumbnail_gd_image);
            imagedestroy($img_fit);
        }

        imagedestroy($source_gd_image);
        
        return $dest;
        //General::flushJsonResponse(['ack'=>'ok', 'url'=>$cache_file]);

    }

    public static function flushJsonResponse(Array $a, $statusCode = 200, $base64encode = false)
    {
        $j = json_encode($a);
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        $f3 = \Base::instance();
        $f3->status($statusCode);
        if ($base64encode) {
            $j = base64_encode($j);
        }
        die($j);
    }

    public static function getCurrentMySqlDate()
    {
        return date('Y-m-d H:i:s');
    }

    public static function getColors()
    {
        $f3 = \Base::instance();
        $db = $f3->get('DB');

        $colors = $db->exec("SELECT * FROM colors");
        
        return $colors;

    }

    public static function getPersons($id = NULL){

        $f3 = \Base::instance();
        $db = $f3->get('DB');

        $persons = $db->exec("SELECT id, person_name FROM persons ORDER BY person_name");
        if (!$id){
            return $persons;
        } else {
            foreach ($persons as $p) {
                $d['id'] = $p['id'];
                $d['person_name'] = $p['person_name'];
                $d['checked'] = General::personChecked($id, $p['id']);
                $data[] = $d;
            }
            return $data;
        }
    }

    public static function personChecked($album_id, $person_id){
        
        $f3 = \Base::instance();
        $db = $f3->get('DB');

        $persons = $db->exec("SELECT * FROM persons_rel WHERE album_id = '$album_id' AND person_id = '$person_id'");
        if(!empty($persons)){
            $is_chcked = 1;
        } else {
            $is_chcked = 0;
        }
        return $is_chcked;
    }

    public static function makeUrl($title, $area, $count = 0)
    {
        $f3 = \Base::instance();
        $web = \Web::instance();
        $slug = $web->slug($title);
        if ($count == 0) {
            $url = sprintf('/%s/%s', $web->slug($area), $slug);
        } else {
            $url = sprintf('/%s/%s-%d', $web->slug($area), $slug, $count);
        }
        $model = new Mapper($f3->get('DB'), 'urls');
        $model->load(['url=?', $url]);
        if ($model->dry()) {
            return ['url' => $url, 'slug' => $slug];
        } else {
            return self::makeUrl($title, $area, ++$count);
        }

    }

    public static function makeAlbumUrl($name, $date)
    {
        //$f3 = \Base::instance();
        $web = \Web::instance();
        $slug = $web->slug($name);

        $date = new DateTime($date);
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');

        $url = sprintf('/%s/%s/%s/%s', $year, $month, $day, $slug);

        //$model = new Mapper($f3->get('DB'), 'urls');
        //$model->load(['url=?', $url]);
        //if ($model->dry()) {
        return ['url' => $url];
        //}

    }

    public static function makeFileUrl($name, $date)
    {
        //$f3 = \Base::instance();
        $web = \Web::instance();
        $slug = $web->slug($name);

        $date = new DateTime($date);
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');

        $url = sprintf('/%s/%s/%s/%s', $year, $month, $day, $slug);

        //$model = new Mapper($f3->get('DB'), 'urls');
        //$model->load(['url=?', $url]);
        //if ($model->dry()) {
        return ['url' => $url];
        //}

    }

    public static function formatSizeUnits($bytes){
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 1) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 1) . ' kB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public static function throw404() {
        $f3 = \Base::instance();
        $f3->error(404);
    }


}