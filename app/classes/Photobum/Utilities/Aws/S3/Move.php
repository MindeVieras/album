<?php
namespace Photobum\Utilities\Aws\S3;

use Aws\S3\S3Client;
use Photobum\Config;
use Photobum\Utilities\Aws\Aws;

use Photobum\Utilities\Aws\S3\Copy;
use Photobum\Utilities\Aws\S3\Delete;

class Move extends Aws
{

    public function __construct() {
        parent::__construct();
        $this->s3 = $this->getS3();
    }

    public function moveObjects($src, $namePath)
    {
        $bucket = $this->bucket;

        $d = array();

        foreach ($src as $row) {

            $filename = basename($row['src']);
            $source = $row['src'];
            $dest = sprintf('albums/%s/%s', $namePath, $filename);
            
            // make delete array for S3
            $del_keys[] = array('Key' => $source);

            // make src path array
            $p = explode('/', $source);


            // Copy main file
            (new Copy())->copyObject($source, $dest);
            
            // Copy image styles
            if($row['type'] == 'image'){
                foreach ($this->img_styles as $st) {

                    if($p[0] == 'uploads'){
                        $styleSrc = sprintf('uploads/styles/%s/%s', $st['name'], $filename);
                    } elseif ($p[0] == 'albums') {
                        $old_path = str_replace('albums/', '', $source);
                        $styleSrc = sprintf('styles/%s/%s', $st['name'], $old_path);
                    }

                    // make tmp style files delete array for S3
                    $del_keys[] = array('Key' => $styleSrc);

                    $styleDest = sprintf('styles/%s/%s/%s', $st['name'], $namePath, $filename);

                    (new Copy())->copyObject($styleSrc, $styleDest);
                };
            }

            // Copy video sizes
            if($row['type'] == 'video'){
                foreach ($this->video_sizes as $size) {
                    $vthumb = explode('.', $filename);
                    $videoThumbName = $vthumb[0];

                    if($p[0] == 'uploads'){
                        $sizeSrc = sprintf('uploads/videos/%s/%s', $size, $filename);
                        $thumbSrc = sprintf('uploads/videos/%s/%s-00001.jpg', $size, $videoThumbName);
                    } elseif ($p[0] == 'albums') {
                        $old_path = str_replace('albums/', '', $source);
                        $sizeSrc = sprintf('videos/%s/%s', $size, $old_path);

                        $old_thumb = trim(str_replace(['albums/', $filename], '', $source), '/');
                        $thumbSrc = sprintf('videos/%s/%s/%s-00001.jpg', $size, $old_thumb, $videoThumbName);
                    }

                    // make tmp style files delete array for S3
                    $del_keys[] = array('Key' => $sizeSrc);
                    $del_keys[] = array('Key' => $thumbSrc);

                    $sizeDest = sprintf('videos/%s/%s/%s', $size, $namePath, $filename);
                    $thumbDest = sprintf('videos/%s/%s/%s-00001.jpg', $size, $namePath, $videoThumbName);

                    (new Copy())->copyObject($sizeSrc, $sizeDest);
                    (new Copy())->copyObject($thumbSrc, $thumbDest);
                };
            }



            //$data = $d;
        }

        // Delete tmp files from S3
        (new Delete())->deleteObjects($del_keys);

        //return $data;

    }

}