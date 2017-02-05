<?php

namespace Albumas\Utilities;


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

    public static function throw404() {
        $f3 = \Base::instance();
        $f3->error(404);
    }


}