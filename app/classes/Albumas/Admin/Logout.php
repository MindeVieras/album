<?php

namespace Albumas\Admin;

class Logout extends Admin
{
    function view()
    {
        $this->f3->clear('SESSION.cw_cms_admin');
        $this->f3->reroute('/admin');
    }


}