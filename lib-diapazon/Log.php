<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/13/14
 * Time: 12:16 AM
 */

namespace Diapazon;

use Monolog\ErrorHandler;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    /** @var Log */
    private static $instance = null;

    /** @var \Monolog\Logger */
    private $log;

    private function __construct()
    {
        $logDir = __DIR__ . '/../log/';

        $this->log = new Logger('diapazonLog');

        $this->log->pushHandler(new StreamHandler($logDir . 'debug.log', Logger::DEBUG));
        $this->log->pushHandler(new StreamHandler($logDir . 'info.log', Logger::INFO));
        $this->log->pushHandler(new StreamHandler($logDir . 'warning.log', Logger::WARNING));
        $this->log->pushHandler(new StreamHandler($logDir . 'error.log', Logger::ERROR));
        $this->log->pushHandler(new StreamHandler($logDir . 'critical.log', Logger::CRITICAL));

        if (DIAPAZON_ENV == Diapazon::ENV_DEV)
            $this->log->pushHandler(new ChromePHPHandler());
        //ErrorHandler::register($this->log);
    }

    private static function getInstance()
    {
        if (is_null(self::$instance))
            self::$instance = new Log;
        return self::$instance;
    }

    private static function log($level, $str)
    {
        $log    = self::getInstance();
        $logger = $log->log;
        if ($level >= DIAPAZON_LOG_LEVEL)
        {
            switch ($level)
            {
                case Logger::INFO:
                    $logger->addInfo($str);
                    break;
                case Logger::WARNING:
                    $logger->addWarning($str);
                    break;
                case Logger::ERROR:
                    $logger->addError($str);
                    break;
                case Logger::CRITICAL:
                    $logger->addInfo($str);
                    break;
                default:
                    $logger->addDebug($str);
            }
        }
    }

    public static function logError($str)
    {
        self::log(Logger::ERROR, $str);
    }

    public static function logDebug($str)
    {
        self::log(Logger::DEBUG, $str);
    }

    public static function logInfo($str)
    {
        self::log(Logger::INFO, $str);
    }

    public static function logWarning($str)
    {
        self::log(Logger::WARNING, $str);
    }

    public static function logCritical($str)
    {
        self::log(Logger::CRITICAL, $str);
    }
} 