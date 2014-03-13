<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/13/14
 * Time: 3:26 AM
 */

namespace Diapazon\Database;

class Db
{
    private $host;
    private $schema;
    private $name;
    private $user;
    private $password;

    /** @var self */
    private static $instance = null;

    /**
     * @return self
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public static function createFromArray(Array $params)
    {
        $db           = self::$instance = new Db();
        $db->host     = $params['host'];
        $db->schema   = $params['schema'];
        $db->name     = $params['name'];
        $db->user     = $params['user'];
        $db->password = $params['password'];
    }

    private function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}