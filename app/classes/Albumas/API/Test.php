<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 07/09/16
 * Time: 16:28
 */

namespace Music\API;

use Music\Utilities\General;

class Test extends APIController
{

    public function get()
    {

        General::flushJsonResponse(['ack' => 'error', 'msg' => 'Sorry this API isn\'t really RESTful'], 405);
        sd($this->f3->get('ROOT'));
        sd('get');
    }
}
