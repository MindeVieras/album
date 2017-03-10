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