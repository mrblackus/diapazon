<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/19/14
 * Time: 11:04 AM
 */

namespace Diapazon;

abstract class AbstractService
{
    /**
     * String emulating first-class citizen dao class
     * @var AbstractDao DAO name maanged by service
     */
    protected static $dao;

    /**
     * @param array $order
     * @return \Diapazon\AbstractEntity[]
     */
    public static function getAll($order = null)
    {
        $dao = static::$dao;
        return $dao::getAll($order);
    }

    /**
     * @param array $where_clause
     * @param array $order
     * @param int   $where_mode
     * @throws \Exception
     * @return AbstractEntity[]
     */
    public static function where($where_clause, $order = null, $where_mode = AbstractDao::WHERE_MODE_AND)
    {
        $dao = static::$dao;
        return $dao::where($where_clause, $order, $where_mode);
    }

    /**
     * @return int
     */
    public static function count()
    {
        $dao = static::$dao;
        return $dao::count();
    }

    /**
     * @param int   $number
     * @param int   $offset
     * @param array $order
     * @return AbstractEntity[]
     */
    public static function take($number, $offset = 0, $order = null)
    {
        $dao = static::$dao;
        return $dao::take($number, $offset, $order);
    }

    /**
     * @param AbstractEntity $entity
     */
    public static function save(AbstractEntity &$entity)
    {
        $dao = static::$dao;
        $dao::save($entity);
    }

    /**
     * @param AbstractEntity $entity
     */
    public static function delete(AbstractEntity &$entity)
    {
        $dao = static::$dao;
        $dao::delete($entity);
    }

    /**
     * @param AbstractEntity[] $objects
     */
    public static function insertAll(Array $objects)
    {
        $dao = static::$dao;
        $dao::insertAll($objects);
    }
} 