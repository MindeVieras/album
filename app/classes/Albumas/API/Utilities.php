<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 07/09/16
 * Time: 18:10
 */

namespace Music\API;


use Music\Utilities\General;

class Utilities extends APIController
{


    public function generateSlug()
    {
        General::flushJsonResponse( General::makeUrl(
            $this->f3->get('REQUEST.value'),
            $this->f3->get('REQUEST.section')
        ));

    }
}