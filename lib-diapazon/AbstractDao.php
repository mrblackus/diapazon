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
     * Simulate first-class citizen for genericity
     * @var AbstractEntity
     */
    private $class;

    /**
     * @param $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

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
    public function getAll($order = null)
    {
        $class    = $this->class;
        $getAllSP = $class::_getSPGetAll();

        $pdo = PDOS::getInstance();

        $order = self::handleOrder($order);

        if (is_null($order))
            $query = $pdo->prepare('SELECT * FROM ' . $getAllSP . '()');
        else
            $query = $pdo->prepare('SELECT * FROM ' . $getAllSP . '() ORDER BY ' . $order);
        $query->execute();

        $datas = $query->fetchAll();

        $outputs = array();
        foreach ($datas as $d)
        {
            $object = new $class();
            self::hydrate($object, $d);
            $outputs[] = $object;
        }
        return $outputs;
    }

    /**
     * @param array $order
     * @throws \Exception
     * @return string|null
     */
    private function handleOrder($order)
    {
        $class = $this->class;
        if (is_array($order))
        {
            $sReturningOrder = '';
            foreach ($order as $sRow)
            {
                $aChunks = explode(' ', $sRow);
                if (in_array($aChunks[0], $class::_getFields()))
                {
                    $sReturningOrder .= $aChunks[0];
                    if (count($aChunks) > 1)
                    {
                        $direction = $aChunks[1];
                        if (strtolower($direction) == 'asc' || strtolower($direction) == 'desc')
                            $sReturningOrder .= ' ' . $direction;
                    }
                    $sReturningOrder .= ', ';
                }
                else
                    throw new \Exception('Trying to order by a column that doesn\'t exist on ' . $class::_getTableName());
            }

            return substr($sReturningOrder, 0, -2);
        }
        else
            return null;
    }

    /**
     * @param       $where_clause
     * @param array $order
     * @param int   $where_mode
     * @throws \Exception
     * @return mixed
     */
    public function where($where_clause, $order = null, $where_mode = self::WHERE_MODE_AND)
    {
        $class    = $this->class;
        $SPGetAll = $class::_getSPGetAll();
        $pdo      = PDOS::getInstance();

        $sWhereClause = '';
        if (is_array($where_clause))
        {
            $sSeparator = $where_mode == self::WHERE_MODE_OR ? 'OR' : 'AND';

            //array(array(field, operator, value), array(field, operator, value))
            foreach ($where_clause as $aCondition)
            {
                if (is_array($aCondition) && count($aCondition) == 3)
                {
                    $column   = $aCondition[0];
                    $operator = $aCondition[1];
                    $value    = $aCondition[2];

                    if (in_array($column, $class::_getFields()))
                    {
                        $valid_operators = array('=', '>', '<', '>=', '<=', '<>', 'IS', 'IS NOT', 'LIKE');
                        if (in_array($operator, $valid_operators))
                        {
                            if (is_bool($value))
                                $value = $value ? 'true' : 'false';
                            else if (is_string($value))
                            {
                                $value = pg_escape_string($value);
                                $value = '\'' . $value . '\'';
                            }
                            else if (is_null($value))
                                $value = 'NULL';

                            $sWhereClause .= $column . ' ' . $operator . ' ' . $value . ' ' . $sSeparator . ' ';
                        }
                        else
                            throw new \Exception('Operator ' . $operator . ' is not a valid operator. Authorized operators are : ' . implode(', ', $valid_operators));
                    }
                    else
                        throw new \Exception($column . ' is not a column of table ' . $class::_getTableName());
                }
                else
                    throw new \Exception('Conditions must be arrays of this kind array(column, operator, value)');
            }

            //Removing that last AND or OR
            $sWhereClause = substr($sWhereClause, 0, -1 * (strlen($sSeparator) + 2));
        }

        $order = self::handleOrder($order);
        if (!is_null($order))
            $order = ' ORDER BY ' . $order;

        $query = $pdo->prepare('SELECT * FROM ' . $SPGetAll . '() WHERE ' . $sWhereClause . $order);
        $query->execute();

        $datas = $query->fetchAll();

        $outputs = array();
        foreach ($datas as $d)
        {
            $object = new $class();
            self::hydrate($object, $d);
            $outputs[] = $object;
        }
        return $outputs;
    }

    /**
     * @return int
     */
    public function count()
    {
        $class = $this->class;
        $proc  = $class::_getSPCount();
        $pdo   = PDOS::getInstance();

        $query = $pdo->prepare('SELECT * FROM ' . $proc . '()');
        $query->execute();

        $result = $query->fetch();

        return intval($result[$proc]);
    }

    /**
     * @param int   $number
     * @param int   $offset
     * @param array $order
     * @return mixed
     */
    public function take($number, $offset = 0, $order = null)
    {
        $class = $this->class;
        $proc  = $class::_getSPTake();
        $pdo   = PDOS::getInstance();

        $order = self::handleOrder($order);

        $query = $pdo->prepare('SELECT * FROM ' . $proc . '(:start, :number, :order)');
        $query->bindValue(':start', $offset);
        $query->bindValue(':number', $number);
        $query->bindValue(':order', is_null($order) ? 'null' : $order);
        $query->execute();

        $datas   = $query->fetchAll();
        $outputs = array();
        foreach ($datas as $d)
        {
            $object = new $class();
            self::hydrate($object, $d);
            $outputs[] = $object;
        }
        return $outputs;
    }

    /**
     * @param AbstractEntity $entity Entity to save
     */
    public function save(AbstractEntity &$entity)
    {
        if ($entity->_getDFEdited())
        {
            if (!$entity->_getDFInserted())
                $this->insert($entity);
            else
                $this->update($entity);
        }
    }

    /**
     * @param AbstractEntity $entity
     */
    private function insert(AbstractEntity &$entity)
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

    private function update(AbstractEntity &$entity)
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