<?php
/**
 * dbog .../src/syncer/Runner.php
 */

namespace Src\Syncer;


use Src\Core\Column;
use Src\Core\Datatype\DtEnum;
use Src\Core\Datatype\DtSet;
use Src\Core\Key;
use Src\Core\Key\Index;
use Src\Core\Key\Primary;
use Src\Core\Key\Unique;
use Src\Core\Relation\Mapping;
use Src\Core\Schema;
use Src\Core\Table;
use Src\Core\Table\Config;
use Src\Core\Trigger;
use Src\Core\View;
use Src\Database\AdapterInterface;
use Src\Exceptions\SyncerException;
use Src\Logger;

class Runner
{
    /** @var AdapterInterface */
    protected $db;

    /** @var Schema */
    protected $schema;

    /** @var Table[] */
    protected $tables;

    /** @var View[] */
    protected $views;

    /** @var string */
    protected $dbSchemaName;

    /** @var bool */
    protected $dryRun = false;

    /** @var Logger */
    protected $logger;


    /**
     * @param AdapterInterface $db
     * @param Schema $schema
     * @param string $dbSchemaName
     */
    public function __construct($db, $schema, $dbSchemaName)
    {
        $this->db = $db;
        $this->schema = $schema;
        $this->tables = $this->schema->getAllTables();
        $this->views = $this->schema->getAllViews();
        $this->dbSchemaName = $dbSchemaName;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $enabled bool
     */
    public function setDryRunMode($enabled)
    {
        $this->dryRun = $enabled;
    }


    /**
     * Sync whole database structure.
     * @throws SyncerException
     */
    public function syncStructure()
    {
        $this->log("Validate database structure.");
        $this->schema->validate();
        $this->log("Database structure valid.");

        $this->log("Syncing database structure.");
        $this->processQuery("USE `$this->dbSchemaName`", true);
        $this->log("SYNC: Switching to schema $this->dbSchemaName.");
        $this->syncSchema();
        // temporarily disable a foreign key constraint
        $this->foreignKeyChecks(false);
        // first mapping sync before table changes
        $this->syncMappings(true);
        $this->syncTables();
        $this->syncViews();
        // final mapping sync after table changes
        $this->syncMappings();
        // enable a foreign key constraint
        $this->foreignKeyChecks(true);
        $this->log("Finished successfully.");
    }

    /**
     * Sync database schema.
     */
    protected function syncSchema()
    {
        $q = "
SELECT `S`.`DEFAULT_CHARACTER_SET_NAME` AS charset, `S`.`DEFAULT_COLLATION_NAME` AS collation
FROM `information_schema`.`SCHEMATA` AS `S`
WHERE `SCHEMA_NAME` = '$this->dbSchemaName'";
        $r = $this->db->fetch($q);

        if ($r['charset'] != $this->schema->getDbCharset())
        {
            $this->log("SYNC: Changing schema $this->dbSchemaName character set to {$this->schema->getDbCharset()}.");
            $this->processQuery("ALTER SCHEMA `$this->dbSchemaName` CHARACTER SET '{$this->schema->getDbCharset()}'");
        }

        if ($r['collation'] != $this->schema->getDbCollation())
        {
            $this->log("SYNC: Changing schema $this->dbSchemaName collation to {$this->schema->getDbCollation()}.");
            $this->processQuery("ALTER SCHEMA `$this->dbSchemaName` COLLATE '{$this->schema->getDbCollation()}'");
        }
    }

    /**
     * Enable/disable foreign key checks.
     * @param bool $enabled
     */
    protected function foreignKeyChecks($enabled)
    {
        $enabled = $enabled ? 1 : 0;
        $this->processQuery("SET foreign_key_checks = " . $enabled);
    }

    /**
     * Sync database tables.
     */
    protected function syncTables()
    {
        $q = "
SELECT `T`.`TABLE_NAME` AS name
FROM `information_schema`.`TABLES` AS `T`
WHERE `T`.`TABLE_SCHEMA` = '$this->dbSchemaName' AND `T`.`TABLE_TYPE` = 'BASE TABLE'";

        $dbTables = [];
        $rs = $this->db->query($q)->fetchAll();
        foreach ($rs as &$r)
        {
            $dbTables[$r['name']] = '';
        }

        foreach ($this->tables as $name => $table)
        {
            $config = $table->getConfiguration();
            $renamedFrom = $config->getRenamedFrom();
            $this->syncTable($config);
            unset ($dbTables[$name], $dbTables[$renamedFrom]);
        }

        foreach ($dbTables as $name => $dummy)
        {
            $this->log("SYNC: Removing table $name.");
            $this->processQuery("DROP TABLE `$name`");
        }
    }

    /**
     * Sync database table.
     * @param Config $tableConfig
     */
    protected function syncTable($tableConfig)
    {
        $tableName = $tableConfig->getName();
        $tableRenamedFrom = $tableConfig->getRenamedFrom();

        $q = "
SELECT `T`.`ENGINE` AS engine, `T`.`TABLE_COLLATION` AS collation
FROM `information_schema`.`TABLES` AS `T`
WHERE `T`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `T`.`TABLE_NAME` = '$tableName'";

        $renameTable = false;
        $r = $this->db->query($q)->fetch();

        if (!$r && !is_null($tableConfig->getRenamedFrom()))
        {
            $q = "
SELECT `T`.`ENGINE` AS engine, `T`.`TABLE_COLLATION` AS collation
FROM `information_schema`.`TABLES` AS `T`
WHERE `T`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `T`.`TABLE_NAME` = '$tableRenamedFrom'";

            $r = $this->db->query($q)->fetch();
            $renameTable = true;
        }

        if ($r)
        {
            if ($renameTable)
            {
                $this->log("SYNC: Changing table $tableRenamedFrom name to $tableName.");
                $this->processQuery("ALTER TABLE `$tableRenamedFrom` RENAME TO `$tableName`");
            }

            if (strtolower($r['engine']) != $this->schema->getEngine())
            {
                $this->log("SYNC: Changing table $tableName engine to {$this->schema->getEngine()}.");
                $this->processQuery("ALTER TABLE `$tableName` ENGINE '{$this->schema->getEngine()}'");
            }

            if (strtolower($r['collation']) != $this->schema->getDbCollation())
            {
                $this->log("SYNC: Changing table $tableName collation to {$this->schema->getDbCollation()}.");
                $this->processQuery("ALTER TABLE `$tableName` COLLATE '{$this->schema->getDbCollation()}'");
            }

            $this->syncTableColumns($tableConfig);
            $this->syncPrimaryKey($tableConfig->getKeyPrimary());
            $this->syncTableIndexes($tableConfig);
            $this->syncTableUniques($tableConfig);

        }
        else
        {
            $this->log("SYNC: Creating table $tableName.");
            $sqlRows = [];

            foreach ($tableConfig->getColumns() as $name => $column)
            {
                $this->log("SYNC: Creating column $name.");
                $sqlRows[$name] = $this->getColumnSQLCreate($column, false);
            }

            $primary = $tableConfig->getKeyPrimary();
            $this->log("SYNC: Creating key primary {$primary->getName()}.");

            $sqlRows[$primary->getName()] = $this->getPrimarySQLCreate($primary);

            foreach ($tableConfig->getKeysUnique() as $unique)
            {
                $name = $unique->getName();
                $this->log("SYNC: Creating key unique $name.");
                $sqlRows[$name] = $this->getUniqueSQLCreate($unique);
            }

            foreach ($tableConfig->getKeysIndex() as $index)
            {
                $name = $index->getName();
                $this->log("SYNC: Creating key index $name.");
                $sqlRows[$name] = $this->getIndexSQLCreate($index);
            }

            $this->processQuery("CREATE TABLE `$tableName` (\n" . implode(",\n", $sqlRows) . "\n) ENGINE '{$this->engine}'");
        }

        $this->syncTableTriggers($tableConfig);
    }

    /**
     * Sync views with database
     */
    protected function syncViews()
    {
        $q = "
SELECT `V`.`TABLE_NAME` AS name
FROM `information_schema`.`VIEWS` AS `V`
WHERE `V`.`TABLE_SCHEMA` = '$this->dbSchemaName'";

        $dbViews = [];
        $rs = $this->db->fetchAll($q);
        foreach ($rs as $r)
        {
            $dbViews[$r['name']] = '';
        }

        foreach ($this->views as $name => $view)
        {
            $this->syncView($view->getConfiguration());
            unset ($dbViews[$name]);
        }

        foreach ($dbViews as $name => $dummy)
        {
            $this->log("SYNC: Removing view $name.");
            $this->processQuery("DROP VIEW `$name`");
        }
    }

    /**
     * @param View\Config $view
     */
    public function syncView($view)
    {
        $q = "
SELECT `V`.`VIEW_DEFINITION` AS definition
FROM `information_schema`.`VIEWS` AS `V`
WHERE `V`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `V`.`TABLE_NAME` = '{$view->getName()}'";

        $r = $this->db->fetch($q);
        if ($r)
        {

            //Definition from information schema has unusable format (includes db name)
            $r = $this->db->fetch("SHOW CREATE VIEW `{$view->getName()}`");
            $dbDefinition = $r['Create View'];
            $parts = explode("VIEW `{$view->getName()}` AS ", $dbDefinition, 2);
            $dbDefinition = $parts[1];

            if ($dbDefinition != $view->getQuery())
            {
                $this->log("SYNC: Changing view {$view->getName()} query.");
                $this->processQuery("ALTER VIEW {$view->getName()} AS {$view->getQuery()}");
            }
        }
        else
        {
            $this->log("SYNC: Creating view {$view->getName()}.");
            $this->processQuery("CREATE VIEW {$view->getName()} AS {$view->getQuery()}");
        }
    }


    /**
     * Get SQL create statement - primary key.
     * @param Primary $primary
     * @return string
     */
    protected function getPrimarySQLCreate($primary)
    {
        return "CONSTRAINT `{$primary->getName()}` PRIMARY KEY (" . $this->getColumnsListToSQL($primary) . ')';
    }

    /**
     * Get SQL create statement - unique key.
     * @param Unique $unique
     * @return string
     */
    protected function getUniqueSQLCreate($unique)
    {
        return "CONSTRAINT `{$unique->getName()}` UNIQUE `{$unique->getName()}` (" . $this->getIndexSQLCreateColumnsList($unique) . ')';
    }

    /**
     * Get SQL create statement - index key.
     * @param Index $index
     * @return string
     */
    protected function getIndexSQLCreate($index)
    {
        return "INDEX `{$index->getName()}` (" . $this->getIndexSQLCreateColumnsList($index) . ')';
    }

    /**
     * Get string with list of columns for SQL statement.
     * @param Primary $key
     * @return string
     */
    protected function getColumnsListToSQL($key)
    {
        $columnsList = [];
        foreach ($key->getColumns() as $i => $column)
        {
            $columnsList[] = $column;
        }

        return "`" . implode('`, `', $columnsList)  . '`';
    }

    /**
     * Get columns list for SQL create statement.
     * @param Key $index
     * @return string
     */
    protected function getIndexSQLCreateColumnsList($index)
    {
        $columnsList = [];
        foreach ($index->getColumns() as $i => $column)
        {
            $prefixLength = '';
            if ($length = $index->getPrefixLength($column))
            {
                $prefixLength = "($length)";
            }

            $columnsList[] = "`$column`$prefixLength";
        }
        return implode(', ', $columnsList);
    }

    /**
     * Sync indexes with database.
     * @param Config $table
     */
    protected function syncTableIndexes($table)
    {
        $tableName = $table->getName();

        $q = "
SELECT `S`.`INDEX_NAME` AS name
FROM `information_schema`.`STATISTICS` AS `S`
WHERE `S`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `S`.`TABLE_NAME` = '$tableName' AND `S`.`NON_UNIQUE` = 1 AND `S`.`INDEX_NAME` != 'PRIMARY'
GROUP BY `S`.`INDEX_NAME`";

        $dbIndexes = [];
        $rs = $this->db->fetchAll($q);

        foreach ($rs as &$r)
        {
            $dbIndexes[$r['name']] = '';
        }

        $indexes = $table->getKeysIndex();

        foreach ($indexes as $index)
        {
            $name = $index->getName();
            $this->syncIndex($table, $index);
            unset ($dbIndexes[$name]);
        }

        foreach ($dbIndexes as $name => $dummy)
        {
            $this->log("SYNC: Removing key index $name.");
            try
            {
                $this->processQuery("ALTER TABLE `$tableName` DROP INDEX `$name`");
            }
            catch (\Exception $exception)
            {
                $this->log("SYNC: Removing key index $name FAILED!");
            }
        }
    }

    /**
     * Sync uniques with database.
     * @param Config $table
     */
    protected function syncTableUniques($table)
    {
        $tableName = $this->getDbTableName($table);

        $q = "
SELECT `S`.`INDEX_NAME` AS name
FROM `information_schema`.`STATISTICS` AS `S`
WHERE `S`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `S`.`TABLE_NAME` = '$tableName' AND `S`.`NON_UNIQUE` = 0 AND `S`.`INDEX_NAME` != 'PRIMARY'
GROUP BY `S`.`INDEX_NAME`";

        $dbUniques = [];
        $rs = $this->db->fetchAll($q);
        foreach ($rs as &$r)
        {
            $dbUniques[$r['name']] = '';
        }

        foreach ($table->getKeysUnique() as $unique)
        {
            $name = $unique->getName();
            $this->syncUnique($table, $unique);
            unset ($dbUniques[$name]);
        }

        foreach ($dbUniques as $name => $dummy)
        {
            $this->log("SYNC: Removing key unique $name.");
            $this->processQuery("ALTER TABLE `$tableName` DROP INDEX `$name`");
        }
    }

    /**
     * Sync triggers with database.
     * @param Config $table
     */
    protected function syncTableTriggers($table)
    {
        $tableName = $table->getName();

        $q = "
SELECT `T`.`ACTION_TIMING` AS timing, `T`.`EVENT_MANIPULATION` AS event, `T`.`TRIGGER_NAME` AS name
FROM `information_schema`.`TRIGGERS` AS `T`
WHERE `T`.`TRIGGER_SCHEMA` = '{$this->dbSchemaName}' AND `T`.`EVENT_OBJECT_TABLE` = '{$tableName}'";

        $dbTriggers = [];
        $rs = $this->db->fetchAll($q);
        foreach ($rs as $r)
        {
            $dbTriggers[$r['timing']][$r['event']][$r['name']] = '';
        }

        foreach ($table->getTriggers() as $trigger)
        {
            $this->syncTableTrigger($table, $trigger);
            unset ($dbTriggers[$trigger->getTime()][$trigger->getAction()][$trigger->getName()]);
        }

        foreach ($dbTriggers as $time => $dbTrigger)
        {
            foreach ($dbTrigger as $event => $dbTr)
            {
                foreach ($dbTr as $name => $dbT)
                {
                    $this->log("SYNC: Removing trigger $name.");
                    $this->processQuery("DROP TRIGGER IF EXISTS `{$name}`");
                }
            }
        }
    }


    /**
     * Sync table trigger with database.
     *
     * @param Config $table
     * @param Trigger $trigger
     */
    protected function syncTableTrigger($table, $trigger)
    {
        $q = "
SELECT
  `T`.`EVENT_OBJECT_TABLE` AS table_name,
  `T`.`EVENT_MANIPULATION` AS event,
  `T`.`ACTION_TIMING` AS action,
  `T`.`ACTION_STATEMENT` AS body
FROM `information_schema`.`TRIGGERS` AS `T`
WHERE `T`.`TRIGGER_SCHEMA` = '{$this->dbSchemaName}' AND `T`.`TRIGGER_NAME` = '{$trigger->getName()}'";

        $r = $this->db->fetch($q);
        if ($r)
        {
            $recreate = false;
            if ($r['table_name'] != $table->getName())
            {
                $recreate = true;
            }
            if ($r['event'] != $trigger->getAction())
            {
                $recreate = true;
            }
            if ($r['action'] != $trigger->getTime())
            {
                $recreate = true;
            }
            if ($r['body'] != $trigger->getTriggerSQLBody())
            {
                $recreate = true;
            }

            if ($recreate)
            {
                $this->log("SYNC: Recreating trigger {$trigger->getName()}.");
                $this->processQuery("DROP TRIGGER `{$trigger->getName()}`");
                $this->processQuery($this->getTriggerSQLCreate($table, $trigger));
            }
        }
        else
        {
            $this->log("SYNC: Creating trigger {$trigger->getName()}.");
            $this->processQuery($this->getTriggerSQLCreate($table, $trigger));
        }
    }

    /**
     * Get SQL create statement for database trigger.
     * @param Config $table
     * @param Trigger $trigger
     * @return string
     */
    protected function getTriggerSQLCreate($table, $trigger)
    {
        $tableName = $table->getName();

        $sql = "CREATE TRIGGER `{$trigger->getName()}` {$trigger->getTime()} {$trigger->getAction()} ON `$tableName` FOR EACH ROW" . PHP_EOL;
        $sql .= $trigger->getTriggerSQLBody();

        return $sql;
    }

    /**
     * Sync index key with database.
     * @param Config $table
     * @param Index $index
     */
    protected function syncIndex($table, $index)
    {
        $tableName = $table->getName();

        $q = "
SELECT `S`.`COLUMN_NAME` AS name, `S`.`SUB_PART` AS length
FROM `information_schema`.`STATISTICS` AS `S`
WHERE `S`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `S`.`INDEX_NAME` = '{$index->getName()}'
ORDER BY `S`.`SEQ_IN_INDEX`";

        $dbColumns = [];
        $dbColumnLengths = [];
        $recreate = false;
        $rs = $this->db->fetchAll($q);

        if (empty ($rs))
        {
            $this->log("SYNC: Creating key index {$index->getName()}.");
            $sql = "INDEX `{$index->getName()}` (" . $this->getIndexSQLCreateColumnsList($index) . ')';
            $this->processQuery("ALTER TABLE `$tableName` ADD $sql");
        }
        else
        {
            foreach ($rs as $r)
            {
                $dbColumns[] = $r['name'];
                $dbColumnLengths[] = $r['length'];
            }

            foreach ($index->getColumns() as $i => $column)
            {
                if ($dbColumns[$i] != $column || $index->getPrefixLength($column) != $dbColumnLengths[$i])
                {
                    $recreate = true;
                    break;
                }
            }

            if ($recreate)
            {
                $this->log("SYNC: Changing key index {$index->getName()}.");
                $sql = $this->getIndexSQLCreate($index);
                $this->processQuery("ALTER TABLE `$tableName` DROP INDEX `{$index->getName()}`");
                $this->processQuery("ALTER TABLE `$tableName` ADD $sql");
            }
        }
    }

    /**
     * Sync unique key with database.
     * @param Config $table
     * @param Unique $unique
     */
    public function syncUnique($table, $unique)
    {
        $tableName = $table->getName();

        $q = "
SELECT `S`.`COLUMN_NAME` AS name, `S`.`SUB_PART` AS length
FROM `information_schema`.`STATISTICS` AS `S`
WHERE `S`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `S`.`INDEX_NAME` = '{$unique->getName()}'
ORDER BY `S`.`SEQ_IN_INDEX`";

        $dbColumns = [];
        $recreate = false;
        $rs = $this->db->fetchAll($q);

        if (empty ($rs))
        {
            $this->log("SYNC: Creating key unique {$unique->getName()}.");
            $sql = $this->getUniqueSQLCreate($unique);
            $this->processQuery("ALTER TABLE `$tableName` ADD $sql");
        }
        else
        {
            foreach ($rs as &$r)
            {
                $dbColumns[] = $r['name'];
            }

            $columns = $unique->getColumns();
            foreach ($columns as $i => $column)
            {
                if ($dbColumns[$i] != $column)
                {
                    $recreate = true;
                    break;
                }
            }

            if ($recreate)
            {
                $this->log("SYNC: Changing key unique {$unique->getName()}.");
                $sql = $this->getUniqueSQLCreate($unique);
                $this->processQuery("ALTER TABLE `$tableName` DROP INDEX `{$unique->getName()}`");
                $this->processQuery("ALTER TABLE `$tableName` ADD $sql");
            }
        }
    }

    /**
     * Sync primary key with database.
     * @param Primary $primary
     */
    protected function syncPrimaryKey($primary)
    {
        $tableName = $primary->getTableName();

        $q = "
SELECT `S`.`COLUMN_NAME` AS column_name
FROM `information_schema`.`STATISTICS` AS `S`
WHERE `S`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `S`.`TABLE_NAME` = '{$tableName}' AND `S`.`INDEX_NAME` = 'PRIMARY'
ORDER BY `S`.`SEQ_IN_INDEX`";

        $dbColumns = [];
        $recreate = false;
        $recreateAI = false;
        $rs = $this->db->fetchAll($q);

        if (!empty ($rs))
        {
            foreach ($rs as $r)
            {
                $dbColumns[] = $r['column_name'];
            }

            $pkColumns = $primary->getColumnInstances();
            foreach ($pkColumns as $i => $column)
            {
                if ($this->isColumnAutoincrementalOnSync($column, false) !== $primary->isAutoincremental())
                {
                    $recreate = true;
                    $recreateAI = true;
                    break;
                }

                if (!isset ($dbColumns[$i]))
                {
                    $recreate = true;
                    break;
                }

                if ($dbColumns[$i] != $column->getName())
                {
                    $recreate = true;
                    break;
                }
            }

            if ($recreateAI)
            {
                $this->log("SYNC: Set key primary {$primary->getName()} auto increment false.");

                foreach ($pkColumns as $column)
                {
                    $sql = $this->getColumnSQLCreate($column);
                    $this->processQuery("ALTER TABLE `{$tableName}` CHANGE  `{$column->getName()}` $sql");
                }
            }

            if ($recreate)
            {
                $this->dropPrimaryConstraints($primary);

                $this->log("SYNC: Changing key primary {$primary->getName()}.");
                $sql = $this->getPrimarySQLCreate($primary);
                $this->processQuery("ALTER TABLE `{$tableName}` DROP PRIMARY KEY");
                $this->processQuery("ALTER TABLE `{$tableName}` ADD $sql");
            }
        }
        else
        {
            $this->log("SYNC: Create key primary {$primary->getName()}.");
            $sql = $this->getPrimarySQLCreate($primary);
            $this->processQuery("ALTER TABLE `{$tableName}` ADD $sql");
        }
    }

    /**
     * Drop primary key column constraints.
     * @param Primary $primary
     */
    protected function dropPrimaryConstraints($primary)
    {
        $columns = $primary->getColumnInstances();
        foreach ($columns as $column)
        {
            $this->dropColumnConstraints($column);
        }
    }

    /**
     * Sync table columns with database.
     * @param Config $tableConfig
     */
    protected function syncTableColumns($tableConfig)
    {
        $tableName = $tableConfig->getName();;

        $q = "
SELECT `C`.`COLUMN_NAME` AS name, `C`.`ORDINAL_POSITION` AS position
FROM `information_schema`.`COLUMNS` AS `C`
WHERE `C`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `C`.`TABLE_NAME` = '$tableName'";

        $dbColumns = [];
        $rs = $this->db->query($q)->fetchAll();
        foreach ($rs as &$r)
        {
            $dbColumns[$r['name']] = '';
        }

        $columns = $tableConfig->getColumns();

        $columnsList = array_keys($columns);
        foreach ($columns as $name => $column)
        {
            $renamedFrom = $column->getRenamedFrom();
            $this->syncTableColumn($column, $columnsList);
            unset ($dbColumns[$name], $dbColumns[$renamedFrom]);
        }

        foreach ($dbColumns as $name => $dummy)
        {
            $this->log("SYNC: Removing column $name.");
            $this->processQuery("ALTER TABLE `$tableName` DROP `$name`");
        }
    }

    /**
     * Sync table column with database.
     * @param Column $column
     * @param $columnsList array
     */
    protected function syncTableColumn($column, $columnsList = [])
    {
        $table = $column->getTable()->getName();
        $renamedTable = $this->getDbTableName($column->getTable());

        $ordinalPositions = array_flip($columnsList);
        $ordinalPosition = $ordinalPositions[$column->getName()];

        $q = "
SELECT
  `C`.`DATA_TYPE` AS data_type,
  `C`.`CHARACTER_MAXIMUM_LENGTH` AS character_maximum_length,
  `C`.`NUMERIC_PRECISION` AS numeric_precision,
  `C`.`NUMERIC_SCALE` AS numeric_scale,
  `C`.`IS_NULLABLE` AS is_nullable,
  `C`.`COLUMN_DEFAULT` AS column_default,
  `C`.`COLLATION_NAME` AS collation_name,
  `C`.`ORDINAL_POSITION` AS ordinal_position,
  `C`.`COLUMN_TYPE` AS column_type,
  IF(`C`.`COLUMN_TYPE` LIKE '%unsigned', 1, 0) as `is_unsigned`
FROM `information_schema`.`COLUMNS` AS `C`
WHERE `C`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `C`.`TABLE_NAME` = :table  AND `C`.`COLUMN_NAME` = :column";

        $renameColumn = false;
        $r = $this->db->query($q, [':table' => $table, ':column' => $column->getName()])->fetch();

        if (!$r && !is_null($column->getRenamedFrom()))
        {
            $r = $this->db->query($q, [':table' => $renamedTable, ':column' => $column->getRenamedFrom()])->fetch();
            $renameColumn = true;
        }

        if ($r)
        {
            $recreate = false;
            $datatype = $column->getDatatype();

            if ($renameColumn)
            {
                $this->log("SYNC: Changing column {$column->getRenamedFrom()} name to {$column->getName()}.");
                $recreate = true;
            }

            if ($r['data_type'] != $datatype->getSqlDatatype())
            {
                $this->log("SYNC: Changing column {$column->getName()} data type to {$datatype->getSqlDefinition()}");
                $recreate = true;
            }

            if ($r['character_maximum_length'] !== null && $datatype->getSqlMaxLength() !== null && $r['character_maximum_length'] != $datatype->getSqlMaxLength())
            {
                $this->log("SYNC: Changing column {$column->getName()} length to {$datatype->getSqlMaxLength()}");
                $recreate = true;
            }

            if ($r['numeric_precision'] !== null && $r['numeric_precision'] != $datatype->getSqlPrecision())
            {

                $this->log("SYNC: Changing column {$column->getName()} precision to {$datatype->getSqlPrecision()}");
                $recreate = true;
            }

            if ($r['numeric_scale'] !== null && $r['numeric_scale'] != $datatype->getSqlScale())
            {
                $this->log("SYNC: Changing column {$column->getName()} scale to {$datatype->getSqlScale()}");
                $recreate = true;
            }

            if ($r['is_nullable'] == 'YES' && !$column->isNull() || $r['is_nullable'] == 'NO' && $column->isNull())
            {
                $this->log("SYNC: Changing column {$column->getName()} nullability to " . ($column->isNull() ? 'YES' : 'NO') . '.');
                $recreate = true;
            }

            $actual = $this->db->quote($r['column_default']);
            $required = $this->db->quote($column->getDefault());

            if ($actual !== $required)
            {
                $this->log("SYNC: Changing column {$column->getName()} default to " . $required);
                $recreate = true;
            }

            if ($r['collation_name'] !== null && $r['collation_name'] != $this->schema->getDbCollation())
            {
                $this->log("SYNC: Changing column {$column->getName()} collation to {$this->schema->getDbCollation()}");
                $recreate = true;
            }

            if ($r['ordinal_position'] !== null && $r['ordinal_position'] != ($ordinalPosition + 1))
            {
                $this->log("SYNC: Changing column {$column->getName()} ordinal position.");
                $recreate = true;
            }

            if ($datatype instanceof DtEnum || $datatype instanceof DtSet)
            {
                if ($r['column_type'] != $datatype->getSqlDefinition())
                {
                    $this->log("SYNC: Changing column {$column->getName()} data type to {$datatype->getSqlDefinition()}");
                    $recreate = true;
                }
            }

            if (!$recreate && ((boolean) $r['is_unsigned'] != $datatype->isUnsigned()))
            {
                $this->log("SYNC: Changing column {$column->getName()} data type to {$datatype->getSqlDefinition()}");
                $recreate = true;
            }


            if ($recreate)
            {
                if ($column->isPrimaryKey())
                {
                    $this->dropColumnConstraints($column);
                }

                if ($column->isForeignKey())
                {
                    $this->dropColumnMapping($table, $column->getName());
                }

                //Drop old primary key if changing to autoincremental
                $primary = $column->getTable()->getKeyPrimary();
                $allowPrimaryKey = false;
                if ($primary->isAutoincremental() && $primary->getColumns() == [$column->getName()])
                {
                    $aiInDb = $this->isColumnAutoincrementalOnSync($column, true);
                    if (!$aiInDb)
                    {
                        $this->dropCurrentPrimaryKeyConstraints($table);
                        $this->processQuery("ALTER TABLE `{$table}` DROP PRIMARY KEY");
                        $allowPrimaryKey = true;
                    }
                }

                $columnPosition = $ordinalPosition == 0 ? ' FIRST' : " AFTER `" . $columnsList[$ordinalPosition - 1] . '`';
                $changedColumn = $renameColumn ? " `{$column->getRenamedFrom()}` " : "`{$column->getName()}`";

                $sql = $this->getColumnSQLCreate($column, $allowPrimaryKey);
                $this->processQuery("ALTER TABLE `{$table}` CHANGE $changedColumn $sql $columnPosition");
            }
        }
        else
        {
            //Drop old primary key if adding new autoincremental

            $primary = $column->getTable()->getKeyPrimary();
            if ($primary->isAutoincremental() && $primary->getColumns() == [$column->getName()])
            {
                $tableName = $column->getTable()->getName();
                $this->dropCurrentPrimaryKeyConstraints($tableName);
                $this->processQuery("ALTER TABLE `{$tableName}` DROP PRIMARY KEY");
            }

            $columnPosition = $ordinalPosition == 0 ? ' FIRST' : " AFTER `" . $columnsList[$ordinalPosition - 1] . "`";
            $this->log("SYNC: Creating column {$column->getName()}.");
            $sql = $this->getColumnSQLCreate($column);
            $this->processQuery("ALTER TABLE `{$table}` ADD $sql $columnPosition");
        }
    }

    /**
     * Drop all constraints for current primary key.
     * @param string $tableName
     */
    protected function dropCurrentPrimaryKeyConstraints($tableName)
    {
        $currentPrimaryColumns = $this->db->fetchColumnAll("
SELECT COLUMN_NAME AS column_name
FROM `information_schema`.`KEY_COLUMN_USAGE`
WHERE `TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `TABLE_NAME` = '{$tableName}' AND `CONSTRAINT_NAME` = 'PRIMARY'
");

        foreach ($currentPrimaryColumns as $columnName)
        {
            $this->dropColumnMapping($tableName, $columnName);
        }
    }

    /**
     * Drop all constraints for specified column.
     * @param Column $column
     */
    protected function dropColumnConstraints($column)
    {
        foreach ($this->tables as $table)
        {
            $tableConfig = $table->getConfiguration();
            $mappings = $tableConfig->getRelationsMapping();

            foreach ($mappings as $mapping)
            {
                $targets = array_combine($mapping->getTargets() ? $mapping->getTargets() : $mapping->getColumns(), $mapping->getColumns());
                if (isset ($targets[$column->getName()]))
                {
                    $tableColumn = $tableConfig->getColumn($targets[$column->getName()]);
                    $tableName = $tableConfig->getName();

                    $this->dropColumnMapping($tableName, $tableColumn->getName());
                }
            }
        }
    }

    /***
     * Drop mapping relation for column.
     * @param string $tableName
     * @param string $columnName
     */
    protected function dropColumnMapping($tableName, $columnName)
    {
        $q = "
SELECT `T`.`CONSTRAINT_NAME`, '' AS `value`
FROM `information_schema`.`TABLE_CONSTRAINTS` AS `T`

INNER JOIN
(
SELECT `KU`.`CONSTRAINT_NAME`
FROM `information_schema`.`KEY_COLUMN_USAGE` AS `KU`
WHERE `KU`.`TABLE_SCHEMA` = '{$this->dbSchemaName}'
AND `KU`.`TABLE_NAME` = '{$tableName}'
AND `KU`.`COLUMN_NAME` = '{$columnName}'
) AS `KU` ON `T`.`CONSTRAINT_NAME` = `KU`.`CONSTRAINT_NAME`
WHERE `T`.`CONSTRAINT_TYPE` = 'FOREIGN KEY'
AND `T`.`TABLE_SCHEMA` = '{$this->dbSchemaName}'
AND `T`.`TABLE_NAME` = '{$tableName}'
";

        $r = $this->db->fetchKeyValue($q);

        if ($r)
        {
            foreach ($r as $name => $dummy)
            {
                $this->log("SYNC: Dropping foreign key {$name}.");
                $this->processQuery("ALTER TABLE `{$tableName}` DROP FOREIGN KEY {$name}");
            }
        }
    }

    /**
     * Sync mappings with database.
     * @param $delete bool
     */
    protected function syncMappings($delete = false)
    {
        foreach ($this->tables as $table)
        {
            $this->syncTableMappings($table->getConfiguration(), $delete);
        }
    }

    /**
     * Whether is incrementable in database
     * @param Column $column
     * @param bool $initPhase
     * @return bool
     */
    protected function isColumnAutoincrementalOnSync($column, $initPhase)
    {
        $table = $column->getTable()->getName();
        $columnName = $initPhase && $column->getRenamedFrom() ? $column->getRenamedFrom() : $column->getName();

        $q = "
SELECT `C`.`EXTRA` = 'auto_increment' AS `autoincremental`
FROM `information_schema`.`COLUMNS` AS `C`
WHERE `C`.`TABLE_SCHEMA` = '{$this->dbSchemaName}' AND `C`.`TABLE_NAME` = '{$table}' AND `C`.`COLUMN_NAME` = '{$columnName}'";

        return (boolean) $this->db->fetchColumn($q);
    }


    /**
     * Sync table mapping relations with database.
     * @param $table Config
     * @param $delete bool
     */
    protected function syncTableMappings($table, $delete = false)
    {

        $dbMappings = $this->getTableDbMappings($table, true);

        $mappings = $table->getRelationsMapping();

        foreach ($mappings as $mapping)
        {
            if (isset ($dbMappings[$mapping->getName()]))
            {
                unset ($dbMappings[$mapping->getName()]);
            }
            elseif (!$delete)
            {
                $this->syncMapping($table, $mapping);
            }
        }

        foreach ($dbMappings as $name => $dummy)
        {
            $this->log("SYNC: Removing mapping {$name}.");
            $this->processQuery("ALTER TABLE `{$table->getName()}` DROP FOREIGN KEY {$name}");
        }
    }

    /**
     * @param Config $table
     * @param Mapping $mapping
     */
    protected function syncMapping($table, $mapping)
    {
        $tableName = $table->getName();

        $q = "
SELECT
  `R`.`TABLE_NAME` AS table_name,
  `R`.`REFERENCED_TABLE_NAME` AS ref_table_name,
  `R`.`DELETE_RULE` AS delete_rule,
  `R`.`UPDATE_RULE` AS update_rule
FROM `INFORMATION_SCHEMA`.`REFERENTIAL_CONSTRAINTS` AS `R`
WHERE `R`.`CONSTRAINT_SCHEMA` = '{$this->dbSchemaName}' AND `R`.`CONSTRAINT_NAME` = '{$mapping->getName()}'";

        $r = $this->db->fetch($q);
        if ($r)
        {
            $recreate = false;
            if ($r['table_name'] != $tableName)
            {
                $recreate = true;
            }
            if ($r['ref_table_name'] != $mapping->getReference())
            {
                $recreate = true;
            }
            if ($r['delete_rule'] != $mapping->getOnDeleteAction())
            {
                $recreate = true;
            }
            if ($r['update_rule'] != $mapping->getOnUpdateAction())
            {
                $recreate = true;
            }

            if ($recreate)
            {
                $this->log("SYNC: Recreating mapping {$mapping->getName()}.");
                $sql = $this->getMappingSQLCreate($mapping);
                $this->processQuery("ALTER TABLE {$r['table_name']} DROP FOREIGN KEY {$mapping->getName()}");
                $this->processQuery("ALTER TABLE `{$tableName}` ADD $sql");
            }
        }
        else
        {
            $this->log("SYNC: Creating mapping {$mapping->getName()}.");
            $sql = $this->getMappingSQLCreate($mapping);
            $this->processQuery("ALTER TABLE `{$tableName}` ADD $sql");
        }
    }

    /**
     * Get SQL create statement - mapping relation.
     * @param Mapping $mapping xebis_model_relation_mapping
     * @return string
     */
    protected function getMappingSQLCreate($mapping)
    {
        $target = count($mapping->getTargets()) ? $mapping->getTargets() : $mapping->getColumns();
        return
            "CONSTRAINT `{$mapping->getName()}` FOREIGN KEY (`" .
            implode('`, `', $mapping->getColumns()) .
            "`) REFERENCES `{$mapping->getReference()}` (`" .
            implode('`, `', $target) . '`)' .
            " ON DELETE " . $mapping->getOnDeleteAction() .
            " ON UPDATE " . $mapping->getOnUpdateAction();
    }


    /**
     * Get table mappings from database.
     * @param Config $table
     * @param boolean $inicialPhase OPTIONAL Whether in initial update phase
     * @return array
     */
    protected function getTableDbMappings($table, $inicialPhase = false)
    {
        $tableName = $table->getName();

        $q = "
SELECT `T`.`CONSTRAINT_NAME`, '' AS `value`
FROM `information_schema`.`TABLE_CONSTRAINTS` AS `T`
WHERE `T`.`CONSTRAINT_TYPE` = 'FOREIGN KEY'
AND `T`.`TABLE_SCHEMA` = '{$this->dbSchemaName}'
AND `T`.`TABLE_NAME` = '{$tableName}'
";

        $r = $this->db->fetchKeyValue($q);

        $renamed = $table->getRenamedFrom() !== null;
        if (empty($r) && $inicialPhase && $renamed)
        {
            $tableName = $table->getRenamedFrom();
            $q = "
SELECT `T`.`CONSTRAINT_NAME`, '' AS `value`
FROM `information_schema`.`TABLE_CONSTRAINTS` AS `T`
WHERE `T`.`CONSTRAINT_TYPE` = 'FOREIGN KEY'
AND `T`.`TABLE_SCHEMA` = '{$this->dbSchemaName}'
AND `T`.`TABLE_NAME` = '{$tableName}'
";

            $r = $this->db->fetchKeyValue($q);
        }

        return $r;
    }

    /**
     * Get SQL create statement for column.
     * @param Column $column
     * @param bool $allowDirectPrimaryKey
     * @return string
     */
    public function getColumnSQLCreate($column, $allowDirectPrimaryKey = true)
    {
        $sql = "`{$column->getName()}` {$column->getDatatype()->getSqlDefinition()} ";
        $sql .= $column->isNull() ? 'NULL' : 'NOT NULL';

        if ($column->getDefault() !== null)
        {
            $sql .= ' DEFAULT ' . $this->db->quote($column->getDefault());
        }

        $primary = $column->getTable()->getKeyPrimary();
        if ($primary->isAutoincremental() && $primary->getColumns() == [$column->getName()])
        {
            //Defined in constraints later
            if ($allowDirectPrimaryKey)
            {
                $sql .= ' PRIMARY KEY';
            }

            $sql .= ' AUTO_INCREMENT';
        }

        return $sql;
    }

    /**
     * Get table name in db.
     * @param Config $tableConfig
     * @param bool $inicialPhase
     * @return string
     */
    protected function getDbTableName($table, $inicialPhase = false)
    {
        $renamed = $table->getRenamedFrom() !== null;
        return $inicialPhase && $renamed ? $table->getRenamedFrom() : $table->getName();
    }

    /**
     * Process query
     * @param string $query SQL query string
     */
    protected function processQuery($query)
    {
        $this->logger->logQuery($query . ';');

        if (!$this->dryRun)
        {
            $this->db->query($query);
        }
    }

    /**
     * Log info message.
     * @param string $message
     */
    protected function log($message)
    {
        $this->logger->logMessage($message);
    }

}
