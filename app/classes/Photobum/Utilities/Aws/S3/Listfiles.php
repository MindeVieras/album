<?php
namespace Photobum\Utilities\Aws\S3;

use Aws\S3\S3Client;
use Photobum\Config;
use Photobum\Utilities\Aws\Aws;

class Listfiles extends Aws
{

    public function __construct() {
        parent::__construct();
        $this->s3 = $this->getS3();
    }

    public function listObjects($path)
    {
        $result = $this->s3->listObjectsV2([
            'Bucket' => $this->bucket,
            //'ContinuationToken' => '<string>',
            //'Delimiter' => $path,
            //'EncodingType' => 'url',
            //'FetchOwner' => true || false,
            //'MaxKeys' => <integer>,
            'Prefix' => $path,
            //'RequestPayer' => 'requester',
            //'StartAfter' => '<string>',
        ]);
        return $result;
    }

}