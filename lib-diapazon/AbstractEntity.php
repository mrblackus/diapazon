<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/13/14
 * Time: 1:41 AM
 */

namespace Diapazon;

abstract class AbstractEntity
{
    /** @var string */
    protected static $_tableName;
    /** @var array */
    protected static $_sequences = array();
    /** @var array */
    protected static $_fields = array();
    /** @var array */
    protected static $_primaryKey = array();
    /** @var string */
    protected static $_SPGetAll;
    /** @var string */
    protected static $_SPCount;
    /** @var string */
    protected static $_SPTake;

    /** @var bool */
    protected $_DFEdited;
    /** @var bool */
    protected $_DFInserted;

    public function __construct()
    {
        $this->_DFEdited   = false;
        $this->_DFInserted = false;
    }

    /**
     * @return string
     */
    public static function _getSPGetAll()
    {
        return static::$_SPGetAll;
    }

    /**
     * @return string
     */
    public static function _getSPCount()
    {
        return static::$_SPCount;
    }

    /**
     * @return string
     */
    public static function _getSPTake()
    {
        return static::$_SPTake;
    }

    /**
     * @return string
     */
    public static function _getTableName()
    {
        return static::$_tableName;
    }

    /**
     * @return array
     */
    public static function _getFields()
    {
        return static::$_fields;
    }

    /**
     * @return array
     */
    public static function _getPrimaryKey()
    {
        return static::$_primaryKey;
    }

    /**
     * @return array
     */
    public static function _getSequences()
    {
        return static::$_sequences;
    }

    /**
     * @param boolean $DFEdited
     */
    public function _setDFEdited($DFEdited)
    {
        $this->_DFEdited = $DFEdited;
    }

    /**
     * @return boolean
     */
    public function _getDFEdited()
    {
        return $this->_DFEdited;
    }

    /**
     * @param boolean $DFInserted
     */
    public function _setDFInserted($DFInserted)
    {
        $this->_DFInserted = $DFInserted;
    }

    /**
     * @return boolean
     */
    public function _getDFInserted()
    {
        return $this->_DFInserted;
    }
} 