<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/13/14
 * Time: 1:54 AM
 */

namespace Diapazon;

use Diapazon\Database\PDOS;

abstract class AbstractDao
{
    const WHERE_MODE_AND = 1;
    const WHERE_MODE_OR  = 2;

    /**
     * @param AbstractEntity $entity Object to hydrate
     * @param array          $data Associative array representing object value
     */
    protected static function hydrate(AbstractEntity &$entity, Array $data)
    {
        $r = new \ReflectionClass($entity);
        foreach ($data as $k => $v)
        {
            $methodName = "set" . Tools::capitalize($k);
            if ($r->hasMethod($methodName))
            {
                $method = $r->getMethod($methodName);
                $method->setAccessible(true);
                $method->invoke($entity, $v);
                $method->setAccessible(false);
            }
        }
    }

    /**
     * @param AbstractEntity $entity
     * @return array
     */
    public static function getAttributes(AbstractEntity $entity)
    {
        $attributes = array();
        $r          = new \ReflectionClass($entity);

        foreach ($r->getProperties() as $att)
        {
            $name = $att->name;
            if (in_array(substr($name, 1), $entity::_getFields()))
            {
                $property = $r->getProperty($name);
                $property->setAccessible(true);
                $attributes[substr($name, 1)] = $property->getValue($entity);
                $property->setAccessible(false);
            }
        }
        return $attributes;
    }

    /**
     * @param array $order Array for ordering results
     * @return AbstractEntity[]
     */
    public abstract function getAll($order = null);

    /**
     * @param array $where
     * @param array $order
     * @param int   $mode
     * @return mixed
     */
    public abstract function where($where, $order = null, $mode = self::WHERE_MODE_AND);

    /**
     * @param int   $number
     * @param int   $offset
     * @param array $order
     * @return mixed
     */
    public abstract function take($number, $offset, $order = null);

    /**
     * @param AbstractEntity $entity Entity to save
     */
    public function save(AbstractEntity &$entity)
    {
        if ($entity->_getDFEdited())
        {
            if (!$entity->_getDFInserted())
                self::insert($entity);
            else
                self::update($entity);
        }
    }

    /**
     * @param AbstractEntity $entity
     */
    private static function insert(AbstractEntity &$entity)
    {
        $aAttributes = self::getAttributes($entity);

        //Detection of attributes to insert : non sequence holder and sequence holder that are null
        $aAttributesToInsert  = array();
        $aSequencedAttributes = array();
        foreach ($aAttributes as $att => $val)
        {
            if (!array_key_exists($att, $entity::_getSequences()) || !is_null($aAttributes[$att]))
                $aAttributesToInsert[$att] = $val;
            else
                $aSequencedAttributes[] = $att;
        }

        //Request building
        $sFields       = '';
        $sPlaceholders = '';

        foreach ($aAttributesToInsert as $att => $val)
        {
            $sFields .= $att . ', ';
            $sPlaceholders .= ':' . $att . ', ';
        }
        $sFields       = substr($sFields, 0, -2);
        $sPlaceholders = substr($sPlaceholders, 0, -2);

        $pdo    = PDOS::getInstance();
        $driver = Diapazon::getDBDriver();
        $query  = $pdo->prepare('INSERT INTO ' . $entity::_getTableName() . ' (' . $sFields . ') VALUES(' . $sPlaceholders . ')');

        foreach ($aAttributesToInsert as $att => $val)
        {
            $driver::bindPDOValue($query, ':' . $att, $val);
        }

        $query->execute();

        //Retrieving sequence values for concerned attributes
        foreach ($aSequencedAttributes as $att)
        {
            $setter = 'set' . Tools::capitalize($att);
            $entity->$setter($pdo->lastInsertId($entity::_getSequences()[$att]));
        }

        $entity->_setDFInserted(true);
        $entity->_setDFEdited(false);
    }

    private static function update(AbstractEntity &$entity)
    {
        $sql        = 'UPDATE ' . $entity::_getTableName() . ' SET ';
        $set        = '';
        $where      = '';
        $attributes = self::getAttributes($entity);

        foreach ($attributes as $k => $v)
            if (!in_array($k, $entity::_getPrimaryKey()))
                $set .= $k . ' = :' . $k . ', ';

        foreach ($entity::_getPrimaryKey() as $pk)
            $where .= $pk . ' = :' . $pk . ' AND ';

        $where = substr($where, 0, -5);
        $set   = substr($set, 0, -2);
        $sql .= $set . ' WHERE ' . $where;

        $pdo    = PDOS::getInstance();
        $query  = $pdo->prepare($sql);
        $driver = Diapazon::getDBDriver();
        foreach ($attributes as $k => $v)
        {
            $driver->bindPDOValue($query, ':' . $k, $v);
        }
        $query->execute();
    }

    /**
     * @param AbstractEntity $entity Entity to delete
     */
    public function delete(AbstractEntity $entity)
    {
        $table       = $entity->_getTableName();
        $pdo         = PDOS::getInstance();
        $whereClause = '';

        foreach ($entity::$primary_keys as $pk)
            $whereClause .= $pk . ' = :' . $pk . ' AND ';

        $whereClause = substr($whereClause, 0, -4);
        $sql         = 'DELETE FROM ' . $table . ' WHERE ' . $whereClause;
        $query       = $pdo->prepare($sql);
        $attributes  = $this->getAttributes($entity);

        foreach ($attributes as $k => $v)
        {
            if (in_array($k, $entity::_getPrimaryKey()))
                $query->bindValue(':' . $k, $v);
        }
        $query->execute();
    }
}