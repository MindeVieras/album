<?php
namespace Photobum\Utilities\S3;

use Aws\S3\S3Client;
use Photobum\Config;
use Photobum\Utilities\Aws;

class Put extends Aws
{

    public function __construct() {
        parent::__construct();
        $this->s3 = $this->getS3();
    }

    public function uploadAlbum($path, $name)
    {
        $bucket = $this->bucket;
        $result = self::putObject($bucket, 'uploads/'.$name, $path);
        $result['UploadURL'] = $this->localisePath($result['ObjectURL']);
        return $result;
    }

    public function putObject($bucket, $key, $source)
    {
        $mime = image_type_to_mime_type(exif_imagetype($source));
        $result = $this->s3->putObject([
            'ACL' => 'public-read',
            'Bucket' => $bucket,
            'Key' => $key,
            'ContentType' => $mime,
            'SourceFile' => $source
        ]);
        return $result;
    }

}