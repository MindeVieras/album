<?php

namespace Photobum\Web;

use Photobum\Utilities\General;
use Photobum\Utilities\Mapper;

class ContactUs extends FrontController {

 public function view() {

     $template = $this->twig->loadTemplate('ContactUs/view.html');
     $this->addBodyClass('contact-us');
     echo $template->render([
        'page' => $this->page
     ]);
 }
}
