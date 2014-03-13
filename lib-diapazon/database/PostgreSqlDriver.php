<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/13/14
 * Time: 4:01 AM
 */

namespace Diapazon\Database;

use PDO;

class PostgreSqlDriver extends Driver
{
    /**
     * @param \PDOStatement $statement
     * @param string        $sParamName
     * @param mixed         $paramValue
     * @return bool
     */
    public static function bindPDOValue(\PDOStatement &$statement, $sParamName, $paramValue)
    {
        if (is_bool($paramValue))
        {
            $iParamType = PDO::PARAM_BOOL;
            $paramValue = $paramValue ? 'true' : 'false';
        }
        else if (is_int($paramValue))
            $iParamType = PDO::PARAM_INT;
        else
            $iParamType = PDO::PARAM_STR;

        return $statement->bindValue($sParamName, $paramValue, $iParamType);
    }

    /**
     * @param string $str
     * @return bool
     */
    public static function isTypeString($str)
    {
        $stringType = array(
            'character varying',
            'character',
            'text'
        );

        return in_array($str, $stringType);
    }
} 