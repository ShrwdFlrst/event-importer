<?php

/**
 * Class Logger
 */
class Logger
{
    /**
     * @param string $message
     */
    public function info(string $message): void
    {
        syslog(LOG_INFO, $message);
//        echo $message.PHP_EOL;
    }

    /**
     * @param string $message
     */
    public function error(string $message): void
    {
        syslog(LOG_ERR, $message);
//        echo $message.PHP_EOL;
    }

    /**
     * @param string $message
     */
    public function warning(string $message): void
    {
        syslog(LOG_WARNING, $message);
//        echo $message.PHP_EOL;
    }
}