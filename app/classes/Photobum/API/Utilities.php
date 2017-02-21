<?php

namespace Photobum\API;


use Photobum\Utilities\General;
use \DateTime;

class Utilities extends APIController
{


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

            $i = 1;

            if (!empty($files)){

                foreach ($files as $f) {
                    $tmp_file = $f['value'];

                    $file_name = basename($tmp_file);
                    if (!file_exists($file_path)) {
                        mkdir($file_path, 0777, true);
                    }

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
                }
    
                $msg = count($files).' files renamed.';
            } else {
                $msg = 'No files suplied';
            }

        }
        General::flushJsonResponse([
            'ack' => $files,
            'msg' => $ats
        ]);
    }

    public function deleteAlbumDir(){
        
        if ($this->f3->get('VERB') == 'POST') {
            $ack = 'ok';
            $data = $this->f3->get('POST');

            $this->rrmdir($data['dir']);


            // $name = \Web::instance()->slug($data['name']);
            // $date = new DateTime($data['start_date']);
            // $year = $date->format('Y');

            // $files = $data['album_images'];
            
            // $ds = DIRECTORY_SEPARATOR;
            // $file_path = getcwd().$ds.'media'.$ds.'albums'.$ds.$year.$ds.$name;

            // $i = 1;

            // if (!empty($files)){

            //     foreach ($files as $f) {
            //         $tmp_file = $f['value'];

            //         $file_name = basename($tmp_file);
            //         if (!file_exists($file_path)) {
            //             mkdir($file_path, 0777, true);
            //         }

            //         rename($tmp_file, $file_path.$ds.$file_name);
            //     };
            // }

        }
        General::flushJsonResponse([
            'ack' => $ack,
            'msg' => $data
        ]);

    }


            public function rrmdir($dir) {
                if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                  if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") 
                       rrmdir($dir."/".$object); 
                    else unlink   ($dir."/".$object);
                  }
                }
                reset($objects);
                rmdir($dir);
                }
            }

    public function getFileSize()
    {
        
        if ($this->f3->get('VERB') == 'POST') {
            $ack = 'ok';
            $size = 0;
            $data = $this->f3->get('POST');
            $file_url = $data['file_url'];

            if (file_exists($file_url)){
                $size = filesize($file_url);
            }
        }
        General::flushJsonResponse([
            'ack' => $ack,
            'msg' => $size
        ]);

    }

    public function getFileSizeRemote(){
        if ($this->f3->get('VERB') == 'POST') {
            $ack = 'ok';
            $data = $this->f3->get('POST');
            $url = $data['file_url_remote'];

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_NOBODY, TRUE);

            $data = curl_exec($ch);
            $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

            curl_close($ch);
        }
      
        General::flushJsonResponse([
            'ack' => $ack,
            'msg' => $size
        ]);

    }
}