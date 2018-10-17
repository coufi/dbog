<?php
/**
 * dbog .../src/core/key/Unique.php
 */

namespace Src\Core\Key;

use Src\Database\AdapterInterface;
use Src\Exceptions\SyncerException;
use Src\Syncer\Runner;

class Unique extends \Src\Core\Key
{

    const UNIQUE_PREFIX = 'uq_';

    /**
     * {@inheritdoc}
     */
    protected function setKeyName()
    {
        $columns = implode('_', $this->columns);
        $this->keyName = substr(self::UNIQUE_PREFIX . $this->getTableName() . '_' . $columns, 0, self::MAX_KEY_NAME_LENGTH);
    }

    /**
     *  Validate unique key.
     * @throws SyncerException
     */
    public function validate()
    {
        parent::validate();

        $columns = $this->table->getColumns();
        foreach ($this->getColumns() as $columnName)
        {
            if (!isset ($columns[$columnName]))
            {
                throw new SyncerException("Unique column {$columnName} not found in table {$this->getTableName()}");
            }
        }
    }

    /**
     * Get SQL create statement.
     * @return string
     */
    public function getSQLCreate()
    {
        return "CONSTRAINT `{$this->getName()}` UNIQUE `{$this->getName()}` (" . $this->getColumnsListToSQL() . ')';
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
SELECT `S`.`COLUMN_NAME` AS name
FROM `information_schema`.`STATISTICS` AS `S`
WHERE `S`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `S`.`INDEX_NAME` = '{$this->getName()}'
ORDER BY `S`.`SEQ_IN_INDEX`";

        return $db->fetchColumnAll($query);
    }

    /**
     * Sync unique key with database.
     * @param Runner $runner
     */
    public function sync($runner)
    {
        $dbColumns = $this->getInformationSchema($runner->getDb(), $runner->getDbSchemaName());
        $recreate = false;

        // found definition in information schema, check for changes
        if ($dbColumns)
        {
            // compare configuration with db definition
            foreach ($this->getColumns() as $i => $column)
            {
                if ($dbColumns[$i] != $column)
                {
                    $recreate = true;
                    break;
                }
            }

            // definition has been changed, sync in db
            if ($recreate)
            {
                $runner->log("SYNC: Changing key unique {$this->getName()}.");
                $runner->processQuery("ALTER TABLE `{$this->getTableName()}` DROP INDEX `{$this->getName()}`");
                $runner->processQuery("ALTER TABLE `{$this->getTableName()}` ADD " . $this->getSQLCreate());
            }
        }
        // definition not found, create new unique key
        else
        {
            $runner->log("SYNC: Creating key unique {$this->getName()}.");
            $runner->processQuery("ALTER TABLE `{$this->getTableName()}` ADD " . $this->getSQLCreate());
        }

    }
}
