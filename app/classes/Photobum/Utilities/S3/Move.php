<?php
namespace Photobum\Utilities\S3;

use Aws\S3\S3Client;
use Photobum\Config;
use Photobum\Utilities\Aws;
use Photobum\Utilities\S3\Delete;

class Move extends Aws
{

    public function __construct() {
        parent::__construct();
        $this->s3 = $this->getS3();
    }

    public function moveObject($src, $key)
    {
        $bucket = $this->bucket;

        $this->s3->copyObject([
            'ACL' => 'public-read',
            'Bucket' => $bucket,
            'Key' => $key,
            'CopySource' => "{$bucket}/{$src}"
        ]);

        // deleteobject after copy
        (new Delete())->deleteObject($src);

    }

}