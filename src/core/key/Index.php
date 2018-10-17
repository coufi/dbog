<?php
/**
 * dbog .../src/core/key/Index.php
 */

namespace Src\Core\Key;

use Src\Database\AdapterInterface;
use Src\Exceptions\SyncerException;
use Src\Syncer\Runner;

class Index extends \Src\Core\Key
{
    const INDEX_PREFIX = 'ix_';

    /** @var array */
    protected $lengths;


    /**
     * {@inheritdoc}
     */
    protected function setKeyName()
    {
        $this->keyName = substr(self::INDEX_PREFIX . $this->getTableName() . '_' . implode('_', $this->columns), 0, self::MAX_KEY_NAME_LENGTH);
    }

    /**
     * Set index column prefix length.
     * @param string $column Column name
     * @param integer $length Prefix length
     * @return Index
     */
    public function setPrefixLength($column, $length)
    {
        $this->lengths[$column] = $length;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefixLength($column)
    {
        return isset ($this->lengths[$column]) ? $this->lengths[$column] : null;
    }

    /**
     * Get SQL create statement.
     * @return string
     */
    public function getSQLCreate()
    {
        return "INDEX `{$this->getName()}` (" . $this->getColumnsListToSQL() . ')';
    }

    /**
     *  Validate index key.
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
                throw new SyncerException("Indexed column {$columnName} not found in table {$this->getTableName()}");
            }
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
SELECT `S`.`COLUMN_NAME` AS name, `S`.`SUB_PART` AS length
FROM `information_schema`.`STATISTICS` AS `S`
WHERE `S`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `S`.`INDEX_NAME` = '{$this->getName()}'
ORDER BY `S`.`SEQ_IN_INDEX`";

        return $db->fetchAll($query);
    }

    /**
     * Sync index key with database.
     * @param Runner $runner
     */
    public function sync($runner)
    {
        $rs = $this->getInformationSchema($runner->getDb(), $runner->getDbSchemaName());
        // found definition in information schema, check for changes
        if ($rs)
        {
            $dbColumns = [];
            $dbColumnLengths = [];
            $recreate = false;
            foreach ($rs as $r)
            {
                $dbColumns[] = $r['name'];
                $dbColumnLengths[] = $r['length'];
            }

            // compare configuration with db definition
            foreach ($this->getColumns() as $i => $column)
            {
                if ($dbColumns[$i] != $column || $this->getPrefixLength($column) != $dbColumnLengths[$i])
                {
                    $recreate = true;
                    break;
                }
            }

            // definition has been changed, sync in db
            if ($recreate)
            {
                $runner->log("SYNC: Changing key index {$this->getName()}.");
                $runner->processQuery("ALTER TABLE `{$this->getTableName()}` DROP INDEX `{$this->getName()}`");
                $runner->processQuery("ALTER TABLE `{$this->getTableName()}` ADD " . $this->getSQLCreate());
            }
        }
        // definition not found, create new index
        else
        {
            $runner->log("SYNC: Creating key index {$this->getName()}.");
            $runner->processQuery("ALTER TABLE `{$this->getTableName()}` ADD " . $this->getSQLCreate());
        }
    }
}
