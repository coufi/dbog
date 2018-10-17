<?php
/**
 * dbog .../src/core/Table.php
 */

namespace Src\Core;


use Src\Core\Table\Config;
use Src\Database\AdapterInterface;
use Src\Exceptions\SyncerException;
use Src\Syncer\Runner;

abstract class Table extends Entity implements ValidableInterface
{
    /** @var string */
    protected $tableName;

    /** @var Config */
    protected $configCache;

    /**
     * @param Schema $schema
     */
    public function __construct($schema)
    {
        $this->tableName = self::getLabel();
        parent::__construct($schema);
    }

    /**
     * Get table name.
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Initialize full configuration for table.
     * @return Config
     */
    protected abstract function initConfiguration();

    /**
     * Get table configuration.
     * @return Config
     */
    public function getConfiguration()
    {
        return $this->configCache ?: ($this->configCache = $this->initConfiguration());
    }

    /**
     * Create new config object.
     * @return Config
     */
    protected function createConfig()
    {
        return new Config($this->tableName, $this->schema);
    }

    /**
     * Validate table's keys and relations
     * @throws SyncerException
     * @todo Advanced validation implementation, if necessary check relation columns datatypes, check relation columns unique indexes, etc.
     */
    public function validate()
    {
        $config = $this->getConfiguration();

        // validate pk
        if (!$config->getKeyPrimary())
        {
            throw new SyncerException("Missing primary key in table {$this->tableName}");
        }

        // validate relations
        foreach ($config->getRelations() as $relation)
        {
            $relation->validate();
        }

        // validate keys
        foreach ($config->getKeys() as $key)
        {
            $key->validate();
        }
    }

    /**
     * Sync table mapping relations with database.
     * @param Runner $runner
     * @param bool $initialPhase Whether table has been udpdated in db already
     */
    public function syncMappings($runner, $initialPhase = false)
    {
        $dbMappings = $this->getDbMappings($runner->getDb(), $runner->getDbSchemaName(), $initialPhase);

        foreach ($this->getConfiguration()->getRelationsMapping() as $mapping)
        {
            // mapping exists both in database and in configuration
            if (isset ($dbMappings[$mapping->getName()]))
            {
                unset ($dbMappings[$mapping->getName()]);
            }
            // do not sync in inicial phase, will be synced later
            elseif (!$initialPhase)
            {
                $mapping->sync($runner);
            }
        }

        // remove mappings from db not being specified in configuration
        foreach (array_keys($dbMappings) as $name)
        {
            $runner->log("SYNC: Removing mapping {$name}.");
            $runner->processQuery("ALTER TABLE `{$this->tableName}` DROP FOREIGN KEY {$name}");
        }
    }

    /**
     * Get information schema from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return bool|array ['engine' => (string), 'collation' => (string), (int) $renamed 0|1]
     */
    protected function getInformationSchema($db, $dbSchemaName)
    {
        $query = "
SELECT `T`.`ENGINE` AS engine, `T`.`TABLE_COLLATION` AS collation, 0 as renamed
FROM `information_schema`.`TABLES` AS `T`
WHERE `T`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `T`.`TABLE_NAME` = '{$this->tableName}'";

        if ($result = $db->fetch($query))
        {
            return $result;
        }

        $result = false;
        $renamedFrom = $this->getConfiguration()->getRenamedFrom();
        if (!is_null($renamedFrom))
        {
            $query = "
SELECT `T`.`ENGINE` AS engine, `T`.`TABLE_COLLATION` AS collation, 1 as renamed
FROM `information_schema`.`TABLES` AS `T`
WHERE `T`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `T`.`TABLE_NAME` = '{$renamedFrom}'";

            $result = $db->fetch($query);
        }

        return $result;
    }

    /**
     * Get table mappings from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @param bool $initialPhase Whether table has been udpdated in db already
     * @return array [(string) $mappingName => (int) 1,... ]
     */
    protected function getDbMappings($db, $dbSchemaName, $initialPhase = false)
    {
        $query = "
SELECT `T`.`CONSTRAINT_NAME`, 1 AS `value`
FROM `information_schema`.`TABLE_CONSTRAINTS` AS `T`
WHERE `T`.`CONSTRAINT_TYPE` = 'FOREIGN KEY'
AND `T`.`TABLE_SCHEMA` = '{$dbSchemaName}'
AND `T`.`TABLE_NAME` = '{$this->tableName}'
";
        if ($r = $db->fetchKeyValue($query))
        {
            return $r;
        }

        $result = [];

        // constraints for table not fount, check whether table has been renamed, initial phase only
        $renamedFrom = $this->getConfiguration()->getRenamedFrom();
        if ($initialPhase && $renamedFrom !== null)
        {
            $query = "
SELECT `T`.`CONSTRAINT_NAME`, '' AS `value`
FROM `information_schema`.`TABLE_CONSTRAINTS` AS `T`
WHERE `T`.`CONSTRAINT_TYPE` = 'FOREIGN KEY'
AND `T`.`TABLE_SCHEMA` = '{$dbSchemaName}'
AND `T`.`TABLE_NAME` = '{$renamedFrom}'
";
            $result = $db->fetchKeyValue($query);
        }

        return $result;
    }

    /**
     * Get table indexes from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array [(string) $indexName => (int) 1,... ]
     */
    protected function getDbIndexes($db, $dbSchemaName)
    {
        $query = "
SELECT `S`.`INDEX_NAME` AS name, 1 AS `value`
FROM `information_schema`.`STATISTICS` AS `S`
WHERE `S`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `S`.`TABLE_NAME` = '{$this->tableName}' AND `S`.`NON_UNIQUE` = 1 AND `S`.`INDEX_NAME` != 'PRIMARY'
GROUP BY `S`.`INDEX_NAME`";

        return $db->fetchKeyValue($query);
    }

    /**
     * Get table unique keys from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array [(string) $uniqueKeyName => (int) 1,... ]
     */
    protected function getDbUniques($db, $dbSchemaName)
    {
        $query = "
SELECT `S`.`INDEX_NAME` AS `name`, 1 AS `value`
FROM `information_schema`.`STATISTICS` AS `S`
WHERE `S`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `S`.`TABLE_NAME` = '{$this->tableName}' AND `S`.`NON_UNIQUE` = 0 AND `S`.`INDEX_NAME` != 'PRIMARY'
GROUP BY `S`.`INDEX_NAME`";

        return $db->fetchKeyValue($query);
    }

    /**
     * Get table columns from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array [[(string) $columnName => (int) $ordinalPosition],... ]
     */
    protected function getDbColumns($db, $dbSchemaName)
    {
        $query = "
SELECT `C`.`COLUMN_NAME` AS name, `C`.`ORDINAL_POSITION` AS position
FROM `information_schema`.`COLUMNS` AS `C`
WHERE `C`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `C`.`TABLE_NAME` = '{$this->tableName}'";

        return $db->fetchKeyValue($query);
    }

    /**
     * Get table triggers from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array [[(string) $triggerTime => [(string) $triggerEvent => [(string) $triggerName => (int)]]],... ]
     */
    protected function getDbTriggers($db, $dbSchemaName)
    {
        $query = "
SELECT `T`.`ACTION_TIMING` AS timing, `T`.`EVENT_MANIPULATION` AS event, `T`.`TRIGGER_NAME` AS name
FROM `information_schema`.`TRIGGERS` AS `T`
WHERE `T`.`TRIGGER_SCHEMA` = '{$dbSchemaName}' AND `T`.`EVENT_OBJECT_TABLE` = '{$this->tableName}'";

        $result = $db->fetchAll($query);

        $dbTriggers = [];
        foreach ($result as $r)
        {
            $dbTriggers[$r['timing']][$r['event']][$r['name']] = 1;
        }

        return $dbTriggers;
    }

    /**
     * Sync database table.
     * @param Runner $runner
     */
    public function sync($runner)
    {
        $result = $this->getInformationSchema($runner->getDb(), $runner->getDbSchemaName());

        // found definition in information schema, check for changes
        if ($result)
        {
            list ($engine, $collation, $renameTable) = $result;

            // table has been renamed
            if ($renameTable)
            {
                $renamedFrom = $this->getConfiguration()->getRenamedFrom();
                $runner->log("SYNC: Changing table {$renamedFrom} name to {$this->tableName}.");
                $runner->processQuery("ALTER TABLE `{$renamedFrom}` RENAME TO `{$this->tableName}`");
            }

            // changed table engine
            if (strtolower($engine) != $this->schema->getEngine())
            {
                // always drop all constraints before engine change
                $this->getConfiguration()->getKeyPrimary()->dropCurrentPrimaryKeyConstraints($runner);
                $this->dropTableMappings($runner);

                $runner->log("SYNC: Changing table {$this->tableName} engine to {$this->schema->getEngine()}.");
                $runner->processQuery("ALTER TABLE `{$this->tableName}` ENGINE '{$this->schema->getEngine()}'");

                // @todo future implementation - check whether specified engine supports FK constraint
                $this->syncMappings($runner, false);
            }

            // changed db collation
            if (strtolower($collation) != $this->schema->getDbCollation())
            {
                $runner->log("SYNC: Changing table {$this->tableName} collation to {$this->schema->getDbCollation()}.");
                $runner->processQuery("ALTER TABLE `{$this->tableName}` COLLATE '{$this->schema->getDbCollation()}'");
            }

            $this->syncTableColumns($runner);

            $this->getConfiguration()->getKeyPrimary()->sync($runner);

            $this->syncTableIndexes($runner);

            $this->syncTableUniques($runner);
        }
        // definition not found, create new table
        else
        {
            $runner->log("SYNC: Creating table {$this->tableName}.");

            // add column create statements to SQL rows
            $sqlRows = [];
            foreach ($this->getConfiguration()->getColumns() as $name => $column)
            {
                $runner->log("SYNC: Creating column {$name}.");
                $sqlRows[$name] = $column->getSQLCreate($runner->getDb(), false);
            }

            // add pk create statements to SQL rows
            $primary = $this->getConfiguration()->getKeyPrimary();
            $runner->log("SYNC: Creating key primary {$primary->getName()}.");
            $sqlRows[$primary->getName()] = $primary->getSQLCreate();

            // add unique keys create statements to SQL rows
            foreach ($this->getConfiguration()->getKeysUnique() as $unique)
            {
                $name = $unique->getName();
                $runner->log("SYNC: Creating key unique {$name}.");
                $sqlRows[$name] = $unique->getSQLCreate($unique);
            }

            // add index keys create statements to SQL rows
            foreach ($this->getConfiguration()->getKeysIndex() as $index)
            {
                $name = $index->getName();
                $runner->log("SYNC: Creating key index {$name}.");
                $sqlRows[$name] = $index->getSQLCreate();
            }

            $runner->processQuery("CREATE TABLE `{$this->tableName}` (\n" . implode(",\n", $sqlRows) . "\n) ENGINE '{$this->schema->getEngine()}'");
        }

        $this->syncTableTriggers($runner);
    }

    /**
     * Drop all table mappings existing in db.
     * @param Runner $runner
     */
    protected function dropTableMappings($runner)
    {
        $dbMappings = $this->getDbMappings($runner->getDb(), $runner->getDbSchemaName(), true);

        // remove all mappings from db for this table
        foreach (array_keys($dbMappings) as $name)
        {
            $runner->log("SYNC: Removing mapping {$name}.");
            $runner->processQuery("ALTER TABLE `{$this->tableName}` DROP FOREIGN KEY {$name}");
        }
    }

    /**
     * Sync table columns with database.
     * @param Runner $runner
     */
    protected function syncTableColumns($runner)
    {
        $dbColumns = $this->getDbColumns($runner->getDb(), $runner->getDbSchemaName());

        foreach ($this->getConfiguration()->getColumns() as $name => $column)
        {
            $renamedFrom = $column->getRenamedFrom();
            $column->sync($runner);
            // found in configuration, remove from list
            unset ($dbColumns[$name], $dbColumns[$renamedFrom]);
        }

        // drop columns from db not being specified in configuration
        foreach (array_keys($dbColumns) as $name)
        {
            $runner->log("SYNC: Removing column {$name}.");
            $runner->processQuery("ALTER TABLE `{$this->tableName}` DROP `{$name}`");
        }
    }

    /**
     * Sync unique keys with database.
     * @param Runner $runner
     */
    protected function syncTableUniques($runner)
    {
        $dbUniques = $this->getDbUniques($runner->getDb(), $runner->getDbSchemaName());

        foreach ($this->getConfiguration()->getKeysUnique() as $unique)
        {
            $name = $unique->getName();
            $unique->sync($runner);
            // found in configuration, remove from list
            unset ($dbUniques[$name]);
        }

        // drop unique keys from db not being specified in configuration
        foreach (array_keys($dbUniques) as $name)
        {
            $runner->log("SYNC: Removing key unique {$name}.");
            $runner->processQuery("ALTER TABLE `{$this->tableName}` DROP INDEX `{$name}`");
        }
    }

    /**
     * Sync indexes with database.
     * @param Runner $runner
     */
    protected function syncTableIndexes($runner)
    {
        $dbIndexes = $this->getDbIndexes($runner->getDb(), $runner->getDbSchemaName());

        foreach ($this->getConfiguration()->getKeysIndex() as $index)
        {
            $name = $index->getName();
            $index->sync($runner);
            // found in configuration, remove from list
            unset ($dbIndexes[$name]);
        }

        // drop index keys from db not being specified in configuration
        foreach (array_keys($dbIndexes) as $name)
        {
            $runner->log("SYNC: Removing key index {$name}.");
            try
            {
                $runner->processQuery("ALTER TABLE `{$this->tableName}` DROP INDEX `{$name}`");
            }
            catch (\Exception $exception)
            {
                $runner->log("SYNC: Removing key index {$name} FAILED!");
            }
        }
    }

    /**
     * Sync triggers with database.
     * @param Runner $runner
     */
    protected function syncTableTriggers($runner)
    {
        $dbTriggers = $this->getDbTriggers($runner->getDb(), $runner->getDbSchemaName());

        foreach ($this->getConfiguration()->getTriggers() as $trigger)
        {
            $trigger->sync($runner);
            // found in configuration, remove from list
            unset ($dbTriggers[$trigger->getTime()][$trigger->getAction()][$trigger->getName()]);
        }

        // drop index keys from db not being specified in configuration
        foreach ($dbTriggers as $time => $dbTrigger)
        {
            foreach ($dbTrigger as $event => $dbTr)
            {
                foreach ($dbTr as $name => $dbT)
                {
                    $runner->log("SYNC: Removing trigger {$name}.");
                    $runner->processQuery("DROP TRIGGER IF EXISTS `{$name}`");
                }
            }
        }
    }
}
