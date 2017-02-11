<?php

namespace Photobum\API;

use Photobum\Utilities\General;

class Test extends APIController
{

    public function get()
    {

        General::flushJsonResponse(['ack' => 'error', 'msg' => 'Sorry this API isn\'t really RESTful'], 405);
        sd($this->f3->get('ROOT'));
        sd('get');
    }
}
