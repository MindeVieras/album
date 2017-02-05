<?php

namespace Albumas\Admin;
use Albumas\Utilities\General;

class Login extends Admin
{
    function view()
    {

        if ($this->f3->get('VERB') == 'POST') {
            $adminmodel = $this->initOrm('admin_users', true);
            $adminmodel->load(['username=?', $this->f3->get('POST.email')]);
            if ($adminmodel->dry()) {
                General::flushJsonResponse(['ack'=>'error'], 403);
            }
            if (password_verify($this->f3->get('POST.password'),$adminmodel->password)) {
                $adminmodel->last_login = General::getCurrentMySqlDate();
                $adminmodel->save();
                $adminmodel->copyTo('SESSION.cw_cms_admin');
                $this->f3->clear('SESSION.cw_cms_admin.password');
                $this->f3->reroute('/admin');
            }
        }
        $template = $this->twig->loadTemplate('Admin/login.html');
        echo $template->render(array(
            'page' => $this->page
        ));

        die();


    }


}