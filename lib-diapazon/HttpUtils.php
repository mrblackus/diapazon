<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/12/14
 * Time: 11:37 PM
 */

namespace Diapazon;

class HttpUtils
{
    public static function getHttpGetParam($name, $default = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    public static function getHttpPostParam($name, $default = null)
    {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }
} 