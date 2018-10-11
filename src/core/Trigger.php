<?php
/**
 * dbog .../src/core/Trigger.php
 */

namespace Src\Core;


use Src\Database\AdapterInterface;
use Src\Syncer\Runner;

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
    protected $tableName;

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
        $this->tableName = $tableName;
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

    /**
     * Get information schema from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array
     */
    protected function getInformationSchema($db, $dbSchemaName)
    {
        $query = "
SELECT
  `T`.`EVENT_OBJECT_TABLE` AS table_name,
  `T`.`EVENT_MANIPULATION` AS event,
  `T`.`ACTION_TIMING` AS action,
  `T`.`ACTION_STATEMENT` AS body
FROM `information_schema`.`TRIGGERS` AS `T`
WHERE `T`.`TRIGGER_SCHEMA` = '{$dbSchemaName}' AND `T`.`TRIGGER_NAME` = '{$this->getName()}'";

        return $db->fetch($query);
    }

    /**
     * Sync trigger with database.
     * @param Runner $runner
     */
    public function sync($runner)
    {
        $r = $this->getInformationSchema($runner->getDb(), $runner->getDbSchemaName());

        // found definition in information schema, check for changes
        if ($r)
        {
            list ($tableName, $event, $action, $body) = $r;

            $recreate = false;

            // changed table name
            if ($tableName != $this->tableName)
            {
                $recreate = true;
            }

            // changed trigger event
            if ($event != $this->getAction())
            {
                $recreate = true;
            }

            // changed trigger time
            if ($action != $this->getTime())
            {
                $recreate = true;
            }

            // changed trigger body
            if ($body != $this->getTriggerSQLBody())
            {
                $recreate = true;
            }

            // definition has been changed, sync in db
            if ($recreate)
            {
                $runner->log("SYNC: Recreating trigger {$this->getName()}.");
                $runner->processQuery("DROP TRIGGER `{$this->getName()}`");
                $runner->processQuery($this->getSQLCreate());
            }
        }
        // definition not found, create new trigger
        else
        {
            $runner->log("SYNC: Creating trigger {$this->getName()}.");
            $runner->processQuery($this->getSQLCreate());
        }
    }

    /**
     * Get SQL create statement for database trigger.
     * @return string
     */
    protected function getSQLCreate()
    {
        $sql = "CREATE TRIGGER `{$this->getName()}` {$this->getTime()} {$this->getAction()} ON `{$this->tableName}` FOR EACH ROW" . PHP_EOL;
        $sql .= $this->getTriggerSQLBody();
        return $sql;
    }
}