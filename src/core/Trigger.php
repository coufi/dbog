<?php
/**
 * dbog .../src/core/Trigger.php
 */

namespace Src\Core;


class Trigger
{
    const TRIGGER_BEGIN = 'BEGIN';
    const TRIGGER_END = 'END';

    const TIME_BEFORE = 'BEFORE';
    const TIME_AFTER = 'AFTER';

    const ACTION_INSERT = 'INSERT';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';

    /** @var string */
    protected $triggerName;

    /** @var string */
    protected $time;

    /** @var string */
    protected $action;

    /** @var string */
    protected $body;

    /**
     * @param $tableName string
     * @param $time string
     * @param $action string
     * @param $body string
     */
    public function __construct($tableName, $time, $action, $body)
    {
        $this->triggerName = strtolower($tableName . '_' . $time . '_' . $action);
        $this->time = $time;
        $this->action = $action;
        $this->body = $body;
        $this->localized = false;
    }

    /**
     * Get trigger time.
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Get trigger action.
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get trigger body.
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get trigger name.
     * @return string
     */
    public function getName()
    {
        return $this->triggerName;
    }

    /**
     * Get SQL trigger body.
     * @return string
     */
    public function getTriggerSQLBody()
    {
        $body = self::TRIGGER_BEGIN;
        $body .= PHP_EOL;
        $body .= $this->body;
        $body .= PHP_EOL;
        $body .= self::TRIGGER_END;

        return $body;
    }
}