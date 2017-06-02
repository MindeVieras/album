<?php

namespace Photobum;

use Photobum\Utilities\General;
use Photobum\Utilities\Mapper;

class Base
{

    /**
     * @var \Base
     */
    protected $f3;
    /**
     * @var \DB\SQL
     */
    protected $db;


    public function __construct()
    {
        $this->f3 = \Base::instance();
        $this->db = $this->f3->get('DB');
        $this->img_styles = $this->db->exec("SELECT * FROM media_styles ORDER BY id ASC");
        $this->video_sizes = array('md', 'hd', 'fhd');

    }

    /**
     * @param $table
     * @param bool $return
     * @return bool|\DB\SQL\Mapper
     */
    public function initOrm($table, $return = false)
    {
        if (!$return && property_exists($this, 'model') && $this->model instanceof \DB\SQL\Mapper) {
            return false;
        }
        if ($return) {
            return new Mapper($this->db, $table);
        }
        $this->model = new Mapper($this->db, $table);
        return $this->model;
    }

    /**
     * Takes an anonymous function to call if exception is otherwise unhandled
     *
     * @param \Exception $e
     * @param callable $callback
     */
    protected function handleExceptions(\Exception $e, $callback = null)
    {
        ExceptionHandler::handleExceptions($this->f3, $e, $callback);
    }

    /**
     * Find property of any item
     * @param $table
     * @param $id
     * @param $property
     * @return bool
     */

    protected function find($table, $id, $property)
    {
        if (!property_exists($this->models, $table)) {
            $this->models->$table = $this->initOrm($table, true);
        }

        $this->models->$table->load(['id=?', $id]);

        if ($this->models->$table->dry() || !array_key_exists($property, $this->models->$table->cast())) {
            return false;
        }

        return $this->models->$table->$property;
    }


    protected function dumpJsonResponse(Array $a, $statusCode = 200)
    {
        General::flushJsonResponse($a, $statusCode);
    }


}