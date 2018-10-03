<?php
/**
 * dbog .../src/Logger.php
 */

namespace Src;


abstract class Logger
{
    /** @var bool */
    protected $outputLogMessages = true;

    /** @var bool */
    protected $outputQueryStrings = true;

    /**
     * Output log message.
     * @param $string
     */
    abstract protected function log($string);

    /**
     * Log processed query string.
     * @param $query
     */
    public function logQuery($query)
    {
        if ($this->outputQueryStrings)
        {
            $this->log($query);
        }
    }

    /**
     * Log runtime info message.
     * @param string $message
     */
    public function logMessage($message)
    {
        if ($this->outputLogMessages)
        {
            $this->log($message);
        }
    }

    /**
     * Enable|disable logging of processed SQL queries.
     * @param bool $enabled
     */
    public function enableQueriesOutput($enabled = true)
    {
        $this->outputQueryStrings = $enabled;
    }

    /**
     * Enable|disable logging of runtime info messages.
     * @param bool $enabled
     */
    public function enableLogMessages($enabled = true)
    {
        $this->outputLogMessages = $enabled;
    }
}
