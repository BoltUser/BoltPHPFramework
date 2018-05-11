<?php

namespace Bolt\Core;


use Psr\Log\LoggerInterface;

class Container
{
    /**
     * @var array[LoggerInterface] list of loggers
     */
    protected $loggers = [];

    /**
     * Get a logger instance
     *
     * @param string $name
     * @return LoggerInterface
     */
    public function getLogger($name = 'defaultLogger')
    {
        return $this->loggers[$name];
    }

    /**
     * @param string $name the name of the logger to be set
     * @param LoggerInterface $logger A logger instance
     */
    public function setLogger($name, LoggerInterface $logger)
    {
        $this->loggers[$name] = $logger;
    }

    final private function __clone()
    {
    }
}