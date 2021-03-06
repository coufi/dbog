<?php
/**
 * dbog .../src/LoggerPrint.php
 */

namespace Src;


class LoggerPrint extends Logger
{
    /**
     * {@inheritdoc}
     */
    protected function log($message)
    {
        echo $message . "\t" . PHP_EOL;
    }
}
