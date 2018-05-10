<?php

namespace Bolt\Logger;

use Bolt\Core\Container;
use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * A constant defining 'System is unusable' logging level
     */
    const LOG_EMERG = 550;

    /**
     * A constant defining 'Immediate action required' logging level
     */
    const LOG_ALERT = 550;

    /**
     * A constant defining 'Critical conditions' logging level
     */
    const LOG_CRIT = 500;

    /**
     * A constant defining 'Error conditions' logging level
     */
    const LOG_ERR = 400;

    /**
     * A constant defining 'Warning conditions' logging level
     */
    const LOG_WARNING = 300;

    /**
     * A constant defining 'Normal but significant' logging level
     */
    const LOG_NOTICE = 200;

    /**
     * A constant defining 'Informational' logging level
     */
    const LOG_INFO = 200;

    /**
     * A constant defining 'Debug-level messages' logging level
     */
    const LOG_DEBUG = 100;

    /**
     * @var Container
     */
    private static $container;

    public function __construct()
    {

    }

    /**
     * Get the service container instance.
     *
     */
    public static function getContainer()
    {
        if (null === self::$container) {
            self::$container = new Container();
        }

        return self::$container;
    }

    /**
     * Set the service container instance.
     *
     */
    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }

    /**
     * Get the configured logger.
     *
     * @return LoggerInterface Configured log class
     */
    public static function getLogger()
    {
        return self::$container->getLogger();
    }

    /**
     * Logs a message
     * If a logger has been configured, the logger will be used, otherwise the
     * logging message will be discarded without any further action
     *
     * @param string $message The message that will be logged.
     * @param int $level The logging level.
     *
     */
    public static function log($message, $logName = 'log', $level = self::LOG_DEBUG)
    {
        $logger = self::$container->getLogger($logName);

        switch ($level) {
            case self::LOG_EMERG:
                return $logger->emergency($message);
            case self::LOG_ALERT:
                return $logger->alert($message);
            case self::LOG_CRIT:
                return $logger->critical($message);
            case self::LOG_ERR:
                return $logger->error($message);
            case self::LOG_WARNING:
                return $logger->warning($message);
            case self::LOG_NOTICE:
                return $logger->notice($message);
            case self::LOG_INFO:
                return $logger->info($message);
            default:
                return $logger->debug($message);
        }
    }
}