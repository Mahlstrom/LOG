<?php
namespace mahlstrom;

use mahlstrom\monolog\monologColorFormatter;
use mahlstrom\monolog\monologTimeSinceExecProcessor;
use Monolog\Logger;
use Monolog\Processor\MemoryUsageProcessor;

/**
 * Class LOG
 */
abstract class LOG
{

    static public $logLevels = [];
    /** @var Logger[] */
    static private $logs = array();

    /**
     * @param string $name
     * @param bool $colors
     * @param bool $execTime
     * @return Logger
     */
    static public function &getLogger($name = '_', $colors = true, $execTime = true)
    {
        self::initLogger($name, $colors, $execTime);
        return self::$logs[$name];
    }

    /**
     * @param string $name
     * @param bool $colors
     * @param bool $execTime
     */
    static private function initLogger($name = '_', $colors, $execTime)
    {

        if (!isset(self::$logs[$name])) {
            $logLevel = Logger::DEBUG;
            if(array_key_exists($name,self::$logLevels)){
                $logLevel=self::$logLevels[$name];
            }elseif(array_key_exists('default',self::$logLevels)){
                $logLevel=self::$logLevels['default'];
            }
            $log = new Logger($name);
            $log->pushProcessor(new MemoryUsageProcessor());
            if ($execTime) {
                $log->pushProcessor(new monologTimeSinceExecProcessor());
            }

            $logH = new \Monolog\Handler\StreamHandler('php://stdout', $logLevel);
            $logH->setFormatter(new monologColorFormatter($colors));
            $log->pushHandler($logH);

            self::$logs[$name] = $log;
        }
    }
}
