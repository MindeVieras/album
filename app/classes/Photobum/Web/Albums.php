<?php

namespace Photobum\Web;

class Albums extends FrontController
{

  public function view()
  {
     $template = $this->twig->loadTemplate('Web/home.html');
     echo $template->render([
        'page' => $this->page
     ]);
  }

  public function viewOne()
  {
    $year = $this->f3->get('PARAMS.year');
    // if (strlen($letter) > 1) {
    //   $letter = substr($letter, 0,1);
    // }

    $template = $this->twig->loadTemplate('Web/Albums/view-one.html');
    echo $template->render([
      'page' => $this->page,
      'data' => $year
    ]);
  }


}
