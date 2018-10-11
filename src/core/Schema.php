<?php
/**
 * dbog .../src/core/Schema.php
 */

namespace Src\Core;


use Src\Collection;
use Src\Database\AdapterInterface;
use Src\Exceptions\SyncerException;
use Src\Syncer\Runner;

abstract class Schema implements ValidableInterface
{
    const ENGINE_INNODB = 'innodb';
    const DB_CHARSET_UTF8 = 'utf8';
    const DB_COLLATION_UTF8_GENERAL_CI = 'utf8_general_ci';

    /** @var Collection */
    protected $tables;

    /** @var Collection */
    protected $views;

    /** @var  string */
    protected $dbCharset = self::DB_CHARSET_UTF8;

    /** @var  string */
    protected $dbCollation = self::DB_COLLATION_UTF8_GENERAL_CI;

    /** @var  string */
    protected $engine = self::ENGINE_INNODB;

    public function __construct()
    {
        $this->tables = new Collection($this);
        $this->views = new Collection($this);
    }

    /**
     * Register all required classes.
     */
    abstract public function init();

    /**
     * Validate all tables for their keys and relations
     * @throws SyncerException
     */
    public function validate()
    {
        foreach ($this->getAllTables() as $tableName => $table)
        {
            $table->validate();
        }
    }

    /**
     * Get information schema from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array ['charset' => (string), 'collation' => (string)]
     */
    public function getInformationSchema($db, $dbSchemaName)
    {
        $query = "
SELECT `S`.`DEFAULT_CHARACTER_SET_NAME` AS `charset`, `S`.`DEFAULT_COLLATION_NAME` AS `collation`
FROM `information_schema`.`SCHEMATA` AS `S`
WHERE `SCHEMA_NAME` = '$dbSchemaName'";
        return $db->fetch($query);
    }


    /**
     * Get table names from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array [(string) $tableName => (int) 1,... ]
     */
    public function getDbTables($db, $dbSchemaName)
    {
        $query = "
SELECT `T`.`TABLE_NAME` AS name, 1 as value
FROM `information_schema`.`TABLES` AS `T`
WHERE `T`.`TABLE_SCHEMA` = '$dbSchemaName' AND `T`.`TABLE_TYPE` = 'BASE TABLE'";

        return $db->fetchKeyValue($query);
    }

    /**
     * Get view names from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array [(string) $viewName => (int) 1,... ]
     */
    public function getDbViews($db, $dbSchemaName)
    {
        $query = "
SELECT `V`.`TABLE_NAME` AS name, 1 as value
FROM `information_schema`.`VIEWS` AS `V`
WHERE `V`.`TABLE_SCHEMA` = '$dbSchemaName'";

        return $db->fetchKeyValue($query);
    }

    /**
     * Sync database schema structure.
     * @param Runner $runner
     */
    protected function syncStructure($runner)
    {
        list ($charset, $collation) = $this->getInformationSchema($runner->getDb(), $runner->getDbSchemaName());

        // changed db charset, sync in db
        if ($charset != $this->getDbCharset())
        {
            $runner->log("SYNC: Changing schema {$runner->getDbSchemaName()} character set to {$this->getDbCharset()}.");
            $runner->processQuery("ALTER SCHEMA {$runner->getDbSchemaName()} CHARACTER SET '{$this->getDbCharset()}'");
        }

        // changed db collation, sync in db
        if ($collation != $this->getDbCollation())
        {
            $runner->log("SYNC: Changing schema {$runner->getDbSchemaName()} collation to {$this->getDbCollation()}.");
            $runner->processQuery("ALTER SCHEMA {$runner->getDbSchemaName()} COLLATE '{$this->getDbCollation()}'");
        }
    }

    /**
     * Sync all schema mappings with database.
     * @param Runner $runner
     * @param bool $initialPhase Whether table has been udpdated in db already
     */
    protected function syncSchemaMappings($runner, $initialPhase = false)
    {
        foreach ($this->getAllTables() as $table)
        {
            $table->syncMappings($runner, $initialPhase);
        }
    }

    /**
     * Sync database tables.
     * @param Runner $runner
     */
    protected function syncSchemaTables($runner)
    {
        $dbTables = $this->getDbTables($runner->getDb(), $runner->getDbSchemaName());
        foreach ($this->getAllTables() as $name => $table)
        {
            $renamedFrom = $table->getConfiguration()->getRenamedFrom();
            $table->sync($runner);
            // found in configuration, remove from list
            unset ($dbTables[$name], $dbTables[$renamedFrom]);
        }

        // remove tables from db not being specified in configuration
        foreach (array_keys($dbTables) as $name)
        {
            $runner->log("SYNC: Removing table {$name}.");
            $runner->processQuery("DROP TABLE `{$name}`");
        }
    }

    /**
     * Sync database views.
     * @param Runner $runner
     */
    protected function syncSchemaViews($runner)
    {
        $dbViews = $this->getDbViews($runner->getDb(), $runner->getDbSchemaName());

        foreach ($this->getAllViews() as $name => $view)
        {
            $view->sync($runner);
            // found in configuration, remove from list
            unset ($dbViews[$name]);
        }

        // remove views from db not being specified in configuration
        foreach (array_keys($dbViews) as $name)
        {
            $runner->log("SYNC: Removing view {$name}.");
            $runner->processQuery("DROP VIEW `{$name}`");
        }
    }

    /**
     * Sync database structure.
     * @param Runner $runner
     */
    public function sync($runner)
    {
        $this->syncStructure($runner);

        // temporarily disable a foreign key constraint
        $this->foreignKeyChecks($runner, false);

        // first mapping sync before table changes
        $this->syncSchemaMappings($runner, true);

        $this->syncSchemaTables($runner);
        $this->syncSchemaViews($runner);

        // final mapping sync after table changes
        $this->syncSchemaMappings($runner);

        // enable a foreign key constraint
        $this->foreignKeyChecks($runner, true);
    }

    /**
     * Enable/disable foreign key checks.
     * @param Runner $runner
     * @param bool $enabled
     */
    protected function foreignKeyChecks($runner, $enabled)
    {
        $enabled = $enabled ? 1 : 0;
        $runner->processQuery("SET foreign_key_checks = " . $enabled);
    }

    /**
     * @param string $dbCharset
     */
    protected function setDbCharset($dbCharset)
    {
        $this->dbCharset = $dbCharset;
    }

    /**
     * @param string $dbCollation
     */
    protected function setDbCollation($dbCollation)
    {
        $this->dbCollation = $dbCollation;
    }

    /**
     * @param string $engine
     */
    protected function setEngine($engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return string
     */
    public function getDbCharset()
    {
        return $this->dbCharset;
    }

    /**
     * @return string
     */
    public function getDbCollation()
    {
        return $this->dbCollation;
    }

    /**
     * @return string
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Whether has table.
     * @param string $tableName
     * @return bool
     */
    public function hasTable($tableName)
    {
        return $this->tables->has($tableName);
    }

    /**
     * Add new table callback.
     * @param string $className
     */
    public function addTable($className)
    {
        $this->tables->add($className);
    }

    /**
     * Get table instance.
     * @param string $tableName
     * @return Table|null
     */
    public function getTable($tableName)
    {
        return $this->tables->get($tableName);
    }

    /**
     * Get all registered table names
     * @return array
     */
    public function getTableNames()
    {
        return $this->tables->getItemKeys();
    }

    /**
     * Get all instances.
     * @return Table[]
     */
    public function getAllTables()
    {
        return $this->tables->getAll();
    }

    /**
     * Whether has view.
     * @param string $viewName
     * @return bool
     */
    public function hasView($viewName)
    {
        return $this->views->has($viewName);
    }

    /**
     * Add new view callback.
     * @param string $className
     */
    public function addView($className)
    {
        $this->views->add($className);
    }

    /**
     * Get view instance.
     * @param string $viewName
     * @return View|null
     */
    public function getView($viewName)
    {
        return $this->views->get($viewName);
    }

    /**
     * Get all registered view names
     * @return array
     */
    public function getViewNames()
    {
        return $this->views->getItemKeys();
    }

    /**
     * Get all instances.
     * @return View[]
     */
    public function getAllViews()
    {
        return $this->views->getAll();
    }
}
