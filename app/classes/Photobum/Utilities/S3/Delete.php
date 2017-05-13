<?php
namespace Photobum\Utilities\S3;

use Aws\S3\S3Client;
use Photobum\Config;
use Photobum\Utilities\Aws;

class Delete extends Aws
{

    public function __construct() {
        parent::__construct();
        $this->s3 = $this->getS3();
    }

    public function deleteObject($key)
    {
        $bucket = $this->bucket;

        $this->s3->deleteObject([
            'Bucket' => $bucket,
            'Key'    => $key
        ]); 

        //return $result;
    }

}