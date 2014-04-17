<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/12/14
 * Time: 10:50 PM
 */

namespace Diapazon;

use Diapazon\Database\Db;
use Diapazon\Database\Driver;
use Diapazon\Database\DriverType;
use Diapazon\Database\PostgreSqlDriver;
use Diapazon\Router\Route;
use Diapazon\Router\Router;

class Diapazon
{
    const ENV_DEV  = 0;
    const ENV_TEST = 1;
    const ENV_PROD = 2;

    private static $instance = null;

    /** @var Route */
    private $route;

    /** @var Controller */
    private $controller;

    /** @var string */
    private $action;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance))
            self::$instance = new Diapazon();
        return self::$instance;
    }

    /**
     * @throws DiapazonException
     */
    public static function init()
    {
        require_once(__DIR__ . '/../vendor/autoload.php');
        require_once(__DIR__ . '/Autoloader.php');
        require_once(__DIR__ . '/config.php');
        require_once(__DIR__ . '/database/DriverType.php');
        require_once(__DIR__ . '/../config/config.php');
        Autoloader::register();

        require_once(__DIR__ . '/../app/routes.php');
        require_once(__DIR__ . '/../config/database.php');
        if (isset($DiapazonDatabases) && is_array($DiapazonDatabases) &&
            array_key_exists('test', $DiapazonDatabases) && array_key_exists('dev', $DiapazonDatabases) &&
            array_key_exists('prod', $DiapazonDatabases)
        )
        {
            switch (DIAPAZON_ENV)
            {
                case self::ENV_TEST:
                    Db::createFromArray($DiapazonDatabases['test']);
                    break;
                case self::ENV_PROD:
                    Db::createFromArray($DiapazonDatabases['prod']);
                    break;
                default:
                    Db::createFromArray($DiapazonDatabases['dev']);
            }
            unset($DiapazonDatabases);
        }
        else
            throw new DiapazonException('Altered config/database.php file');

        Log::logInfo("Diapazon v" . DIAPAZON_VERSION." initialized");
    }

    private function route()
    {
        //Route determination
        $url = HttpUtils::getHttpGetParam('url', '/');
        Log::logInfo('Route called is ' . $url);

        $route = Router::get($url);
        if (!is_null($route))
            $this->route = $route;
        else
        {
            header("HTTP/1.0 404 Not Found");
            die();
        }
    }

    private function checkRouting()
    {
        $className = $this->route->getControllerName() . 'Controller';
        if (class_exists($className))
        {
            $this->controller = new $className();
            if (method_exists($this->controller, $this->route->getActionName()))
                $this->action = $this->route->getActionName();
            else
                throw new DiapazonException('Action ' . $this->route->getActionName() . ' doesn\'t exist on ' . $this->route->getControllerName() . ' controller.');
        }
        else
            throw new DiapazonException('Controller ' . $this->route->getControllerName() . ' doesn\'t exist.');
    }

    private function execute()
    {
        $this->controller->before_filter($this->route->getParameters());
        $action = $this->action;
        $this->controller->$action($this->route->getParameters());
    }

    /**
     * @throws \Exception
     * @return Driver
     */
    public static function getDBDriver()
    {
        if (DIAPAZON_DB_DRIVER == DriverType::POSTGRESQL)
            $driver = new PostgreSqlDriver();
        else
            throw new \Exception('Invalid database driver');

        return $driver;
    }

    public static function run()
    {
        try
        {
            $diapazon = self::getInstance();
            $diapazon->init();
            $diapazon->route();
            $diapazon->checkRouting();
            $diapazon->execute();
        }
        catch (\Exception $e)
        {
            header("HTTP/1.0 500 Internal Server Error");
            throw $e;
        }
    }
}