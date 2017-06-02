<?php

namespace Photobum\Utilities\Aws;

use Photobum\Config;
use Aws\Sdk;

class Aws extends \Photobum\Base
{
    public function __construct()
    {
        parent::__construct();
        $this->version = 'latest';
        $this->region = 'eu-west-1';
        $this->bucket = 'images.album.mindelis.com';
        $this->sdk = new Sdk([
            'version' => $this->version,
            'region' => $this->region,
            'credentials' => array(
                'key'    => Config::get('AWS_ACCESS_KEY_ID'),
                'secret' => Config::get('AWS_SECRET_KEY'),
            )
        ]);
    }

    protected function localisePath($path) {
        //no cdn
        //return $path;

        //removes the amazon bit from the path
        $path = str_replace(['https://' ,'http://'], '',$path);
        return trim(str_replace(['s3-eu-west-1.amazonaws.com/', $this->bucket], '',$path), '/');
    }

    protected function getS3() {
        return $this->sdk->createS3();
    }

    protected function getTc() {
        return $this->sdk->createElasticTranscoder();
    }


}