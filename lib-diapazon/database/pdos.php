<?php
/**
 * Created by JetBrains PhpStorm.
 * User: savy_m
 * Date: 24/05/13
 * Time: 16:21
 * To change this template use File | Settings | File Templates.
 */

namespace Diapazon\Database;

class PDOS
{
    private static $nbQuery_ = 0;
    private static $instance_;

    /**
     * @throws \Exception
     * @return EPO
     */
    public static function getInstance()
    {
        if (!isset($_instance))
        {
            $db = Db::getInstance();

            try
            {
                self::$instance_ = new EPO('pgsql:host=' . $db->getHost() . ';dbname=' . $db->getName(), $db->getUser(), $db->getPassword());
                self::$instance_->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                self::$instance_->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            }
            catch (\PDOException $e)
            {
                $error = 'Error : ' . $e->getMessage();
                throw new \Exception($error);
            }
        }
        return self::$instance_;
    }

    static function incNbQuery()
    {
        self::$nbQuery_++;
    }

    /**
     * @return int
     */
    public static function getNbQuery()
    {
        return self::$nbQuery_;
    }
}
