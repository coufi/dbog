<?php
/**
 * dbog .../src/core/key/Primary.php
 */

namespace Src\Core\Key;

use Src\Database\AdapterInterface;
use Src\Syncer\Runner;

class Primary extends \Src\Core\Key
{
    const PRIMARY_PREFIX = 'pk_';

    /** @var bool */
    protected $autoincremental;

    /**
     * {@inheritdoc}
     */
    protected function setKeyName()
    {
        $this->keyName = self::PRIMARY_PREFIX . $this->getTableName();
    }

    /**
     * Set autoincremental.
     * @return Primary
     */
    public function setAutoincremental()
    {
        $this->autoincremental = true;
        return $this;
    }

    /**
     * Whether is autoincremental.
     * @return bool
     */
    public function isAutoincremental()
    {
        return $this->autoincremental;
    }

    /**
     * Get SQL create statement - primary key.
     * @return string
     */
    public function getSQLCreate()
    {
        return "CONSTRAINT `{$this->getName()}` PRIMARY KEY (" . $this->getColumnsListToSQL() . ')';
    }

    /**
     * Drop all constraints for current primary key.
     * @param Runner $runner
     */
    public function dropCurrentPrimaryKeyConstraints($runner)
    {
        $currentPrimaryColumns = $runner->getDb()->fetchColumnAll("
SELECT COLUMN_NAME AS column_name
FROM `information_schema`.`KEY_COLUMN_USAGE`
WHERE `TABLE_SCHEMA` = '{$runner->getDbSchemaName()}' AND `TABLE_NAME` = '{$this->getTableName()}' AND `CONSTRAINT_NAME` = 'PRIMARY'
");

        //drop all mapping relations for found pk columns
        foreach ($currentPrimaryColumns as $columnName)
        {
            $column = $this->getTable()->getColumn($columnName);
            $column->dropMapping($runner);
        }
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
SELECT `S`.`COLUMN_NAME` AS column_name
FROM `information_schema`.`STATISTICS` AS `S`
WHERE `S`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `S`.`TABLE_NAME` = '{$this->getTableName()}' AND `S`.`INDEX_NAME` = 'PRIMARY'
ORDER BY `S`.`SEQ_IN_INDEX`";

        return $db->fetchColumnAll($query);
    }

    /**
     * Sync primary key with database.
     * @param Runner $runner
     */
    public function sync($runner)
    {
        $dbColumns = $this->getInformationSchema($runner->getDb(), $runner->getDbSchemaName());
        $recreate = false;
        $recreateAI = false;

        // found definition in information schema, check for changes
        if ($dbColumns)
        {
            // check pk columns for changes
            $pkColumns = $this->getColumnInstances();
            foreach ($pkColumns as $i => $column)
            {
                // changed autoincrement option
                if ($column->isAutoincrementalOnSync($runner->getDb(), $runner->getDbSchemaName(), false) !== $this->isAutoincremental())
                {
                    $recreate = true;
                    $recreateAI = true;
                    break;
                }

                // column does not exist in db definition
                if (!isset ($dbColumns[$i]))
                {
                    $recreate = true;
                    break;
                }

                // different column name in db definition
                if ($dbColumns[$i] != $column->getName())
                {
                    $recreate = true;
                    break;
                }
            }

            // autoincrement option has been changed, sync in db
            if ($recreateAI)
            {
                $runner->log("SYNC: Set key primary {$this->getName()} auto increment false.");

                foreach ($pkColumns as $column)
                {
                    $sql = $column->getSQLCreate($runner->getDb());
                    $runner->processQuery("ALTER TABLE `{$this->getTableName()}` CHANGE  `{$column->getName()}` $sql");
                }
            }

            // primary key has been changed, sync with db
            if ($recreate)
            {
                $this->dropPrimaryConstraints($runner);

                $runner->log("SYNC: Changing key primary {$this->getName()}.");
                $this->processQuery("ALTER TABLE `{$this->getTableName()}` DROP PRIMARY KEY");
                $this->processQuery("ALTER TABLE `{$this->getTableName()}` ADD " . $this->getSQLCreate());
            }
        }
        // definition not found, create new pk
        else
        {
            $this->log("SYNC: Create key primary {$this->getName()}.");
            $this->processQuery("ALTER TABLE `{$this->getTableName()}` ADD " . $this->getSQLCreate());
        }
    }

    /**
     * Drop primary key column constraints.
     * @param Runner $runner
     */
    protected function dropConstraints($runner)
    {
        foreach ($this->getColumnInstances() as $column)
        {
            $column->dropConstraints($runner);
        }
    }
}
