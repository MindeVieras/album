<?php
namespace Photobum\Utilities\Aws\Transcoder;

use Aws\ElasticTrancoder\ElasticTranscoderClient;
use Photobum\Config;
use Photobum\Utilities\Aws\Aws;

class Job extends Aws
{

    public function __construct() {
        parent::__construct();
        $this->tc = $this->getTc();
    }

    public function createJob($src, $dest, $size, $thumbName)
    {
        if ($size == 'md'){
            $preset = '1495400403828-wqj8po';
        } elseif ($size == 'hd'){
            //$preset = '1495400403828-wqj8po';
            $preset = '1495399217775-k8xki9';
        } elseif ($size == 'fhd'){
            $preset = '1495400496449-9wdlte';
        } else {
            // Set 320x240 standard if no size
            $preset = '1351620000001-000061';
        }

        $thumbPattern = sprintf('uploads/videos/%s/%s-', $size, $thumbName);

        $result = $this->tc->createJob([
            'PipelineId' => '1495388973247-jvhq6m',
            'Input' => array(
                'Key' => $src,
                'FrameRate' => 'auto',
                'Resolution' => 'auto',
                'AspectRatio' => 'auto',
                'Interlaced' => 'auto',
                'Container' => 'auto'
            ),
            'Output' => array(
                'Key' => $dest,
                'ThumbnailPattern' => $thumbPattern.'{count}',
                'Rotate' => 'auto',
                'PresetId' => $preset
            )
        ]);
        
        return $result;
    }

}