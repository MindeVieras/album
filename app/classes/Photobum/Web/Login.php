<?php

namespace Photobum\Web;

use Photobum\Utilities\General;

class Login extends FrontController
{
    function view()
    {

        if ($this->f3->get('VERB') == 'POST') {
            $webuser = $this->initOrm('users', true);
            $webuser->load(['username=? and type=\'web\'', $this->f3->get('POST.username')]);
            if ($webuser->dry()) {
                General::flushJsonResponse(['ack'=>'error'], 403);
            }
            if (password_verify($this->f3->get('POST.password'),$webuser->password)) {
                $webuser->last_login = General::getCurrentMySqlDate();
                $webuser->save();
                $webuser->copyTo('SESSION.album_web');
                $this->f3->clear('SESSION.album_web.password');
                $this->f3->reroute('/');
            }
        }
        $template = $this->twig->loadTemplate('Web/login.html');
        echo $template->render(array(
            'page' => $this->page
        ));

        die();


    }


}