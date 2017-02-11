<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 31/08/16
 * Time: 17:15
 */

namespace Photobum\Web;

class Home extends FrontController
{

 public function view()
 {
     $template = $this->twig->loadTemplate('Home/view.html');
     //$this->twig->onReady('Photobum.initHomePage');
     $this->addBodyClass('front');;
     echo $template->render([
         'page' => $this->page
     ]);
 }
}
