<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 31/08/16
 * Time: 17:15
 */

namespace Photobum\Web;


use Photobum\Utilities\General;
use Photobum\Utilities\Mapper;

class News extends FrontController
{
 public function view_block()
 {
     $data = $this->getNews(4);
     $template = $this->twig->loadTemplate('News/view_block.html');
     echo $template->render([
         'news' => $data,
         'page' => $this->page
     ]);
 }

//    public function beforeRoute()
//    {
//        $uri = $this->f3->get('URI');
//        $this->cache_key = \Web::instance()->slug($uri);
//        if ($this->cache->exists($this->cache_key,$this->output)) {
//            echo $this->output;
//            die();
//        }
//    }
//
//    public function afterRoute()
//    {
//        $this->cache->set($this->cache_key, $this->output);
//        echo $this->output;
//    }



 public function view()
 {
    $data = $this->getNews();
     $template = $this->twig->loadTemplate('News/view.html');
     echo $template->render([
         'news' => $data,
         'page' => $this->page
     ]);
 }


 public function viewOne()
 {

     $path = $this->f3->get('PATH');
     $urlsModel = new Mapper($this->db, 'urls');
     $urlsModel->load(['url=?', $path]);


     if ($urlsModel->dry()) {
         General::throw404();
     }

     $newsModel = new Mapper($this->db, 'news_items');
     $newsModel->author =
         'SELECT attribution_name FROM admin_users WHERE news_items.attribution_id = admin_users.id';
     $newsModel->load(['id=?', $urlsModel->type_id]);
     if ((!$newsModel->publish || strtotime($newsModel->publish_date) > time()) && ($this->isAuthed == false)) {
        General::throw404();
     }
     $this->item = $newsModel->cast();
     $this->decoratePrevNexts();
     $template = $this->twig->loadTemplate('News/view_one.html');
     $this->twig->onReady('Photobum.initNewsOne');
     echo $template->render([
         'item' => $this->item,
         'page' => $this->page
     ]);

 }

    public function decoratePrevNexts()
    {
       $this->item['previous'] = [];
       $sql = "SELECT
                   news_items.*,
                   urls.url
                FROM
                   news_items
                JOIN urls ON urls.type_id = news_items.id AND urls.type = 'news'
                WHERE
                   publish_date < :publish_date
                       AND publish = 1
                       AND publish_date < NOW()
                ORDER BY publish_date DESC
                LIMIT 1";
       $res = $this->f3->get('DB')->exec($sql, [':publish_date' => $this->item['publish_date'] ]);
       if (count($res)) {
           $row = array_pop($res);
           $this->item['previous'] = $row;
       }
        $this->item['next'] = [];

        $sql = "SELECT
                    news_items.*,
                    urls.url
                 FROM
                    news_items
                 JOIN urls ON urls.type_id = news_items.id AND urls.type = 'news'
                 WHERE
                    publish_date > :publish_date
                        AND publish = 1
                        AND publish_date < NOW()
                 ORDER BY publish_date ASC
                 LIMIT 1";
        $res = $this->f3->get('DB')->exec($sql, [':publish_date' => $this->item['publish_date']  ]);

        if (count($res)) {
           $row = array_pop($res);
           $this->item['next'] = $row;
        }
    }
     public function getNews($limit = 10)
     {
             $sql = "SELECT
                    i.headline,
                    i.headline_image,
                    i.hilite_para,
                    i.publish_date,
                    u.attribution_name as author,
                    urls.url
                FROM
                    news_items AS i
                    JOIN admin_users as u ON i.attribution_id = u.id
                    JOIN urls  ON i.id = urls.type_id AND urls.type = 'news'
                WHERE
                    i.publish = 1 AND i.publish_date < NOW()
                ORDER BY i.publish_date DESC
                LIMIT ?
                ";
         return $this->db->exec($sql, $limit);
     }
}
