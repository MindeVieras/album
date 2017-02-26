<?php

namespace Photobum\API;


use Photobum\Utilities\General;
use \DateTime;
//use \Image;

use Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class Utilities extends APIController{

    public function generateSlug()
    {
        General::flushJsonResponse( General::makeUrl(
            $this->f3->get('REQUEST.value'),
            $this->f3->get('REQUEST.section')
        ));


    }

    public function renameFiles(){
        
        if ($this->f3->get('VERB') == 'POST') {
            $ack = 'ok';
            $data = $this->f3->get('POST');

            $id = $data['id'];

            $name = \Web::instance()->slug($data['name']);
            $date = new DateTime($data['start_date']);
            $year = $date->format('Y');

            $files = $data['album_images'];
            
            $ds = DIRECTORY_SEPARATOR;
            $file_path = getcwd().$ds.'media'.$ds.'albums'.$ds.$year.$ds.$name;
            $file_path_style = getcwd().$ds.'media'.$ds.'albums'.$ds.$year.$ds.$name.$ds.'styles';
            $styles = $this->db->exec("SELECT * FROM media_styles");

            $i = 1;

            if (!empty($files)){

                $imagine = new Imagine\Gd\Imagine();
                
                foreach ($files as $f) {
                    $tmp_file = $f['value'];

                    $file_name = basename($tmp_file);

                    // make media DIR
                    if (!file_exists($file_path)) {
                        mkdir($file_path, 0777, true);
                    }

                    // make image styles
                    foreach ($styles as $style) {

                        // make media styles DIR's
                        if (!file_exists($file_path_style.$ds.$style['name'])) {
                            mkdir($file_path_style.$ds.$style['name'], 0777, true);
                        }

                        $image = $imagine->open($tmp_file);

                        $image->thumbnail(new Box($style['width'], $style['height']))
                            ->save($file_path_style.$ds.$style['name'].$ds.$file_name);
                        
                    }
                    // move files
                    rename($tmp_file, $file_path.$ds.$file_name);
                };
                
                // get files from DB
                $db_files_url = $this->db->exec("SELECT * FROM media WHERE album_id = $id");
                foreach ($db_files_url as $df) {
                    $db_files[] = basename($df['file_url']);
                }

                // get files form DIR
                $scanned_dir = array_diff(scandir($file_path), array('..', '.'));
                foreach ($scanned_dir as $sc) {
                    $dir_files[] = $sc;
                }

                // compare arrays and get unwated files
                $unwanted_files = array_diff($dir_files, $db_files);

                // remove unwanted files
                foreach ($unwanted_files as $uf) {
                    unlink($file_path.$ds.$uf);
                    // remove unwanted style files
                    foreach ($styles as $style) {
                        unlink($file_path_style.$ds.$style['name'].$ds.$uf);
                    }
                }
                
                $msg = count($files).' files renamed.';
            } else {
                $msg = 'No files suplied';
            }

        }
        General::flushJsonResponse([
            'ack' => $ack,
            'msg' => $msg
        ]);
    }

    public function deleteAlbumDir(){
        
        if ($this->f3->get('VERB') == 'POST') {
            $ack = 'ok';
            $data = $this->f3->get('POST');

            //$this->rrmdir($data['dir']);
            $command = 'rm -Rf '.$data['dir'];
            shell_exec($command);

        }
        General::flushJsonResponse([
            'ack' => $ack,
            'msg' => $data
        ]);
    }

}