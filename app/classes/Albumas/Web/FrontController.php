<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 31/08/16
 * Time: 17:16
 */

namespace Music\Web;

use Music\Base;
use Music\Config;
use Music\Utilities\Twig;

class FrontController extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->twig = new Twig();
        $this->isAuthed =  (bool) $this->f3->get('SESSION.cw_cms_admin');
        $this->twig->onReady('Music.initFrontend');
        $this->page = [
            'base_url' => Config::get('BASE_URL'),
            'environment' => Config::get('ENVIRONMENT'),
            'path' => $this->f3->get('PATH'),
            'meta' => $this->getMeta(),
            'isAuthed' => $this->isAuthed
        ];

    }


    private function getMeta() {
        $seo =  $this->initOrm('seo', true);
        $seo->load(['url=?', $this->f3->get('PATH')]);
        if ($seo->dry()) {
            return [
                'id' => 0,
                'desc' => 'Default description',
                'title' => 'Default title'
            ];
        }
        return [
            'id' => $seo->id,
            'desc' => $seo->meta_desc,
            'title' => $seo->meta_title
        ];
    }

    public function addBodyClass($class)
    {
      $this->page['body_class'][] = $class;
      $this->page['body_class'] = array_unique($this->page['body_class']);
    }

    public function removeBodyClass($class)
    {
      $this->page['body_class'] = array_filter($this->page['body_class'], function($e) use ($class) {
          return ($e !== $class);
      });
    }

}
