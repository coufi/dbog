<?php
/**
 * dbog .../src/core/Schema.php
 */

namespace Src\Core;


use Src\Collection;

abstract class Schema
{
    const ENGINE_INNODB = 'innodb';
    const DB_CHARSET_UTF8 = 'utf8';
    const DB_COLLATION_UTF8_GENERAL_CI = 'utf8_general_ci';

    /** @var Collection[] */
    protected $tables = [];

    /** @var  string */
    protected $dbCharset = self::DB_CHARSET_UTF8;

    /** @var  string */
    protected $dbCollation = self::DB_COLLATION_UTF8_GENERAL_CI;

    /** @var  string */
    protected $engine = self::ENGINE_INNODB;

    public function __construct()
    {
        $this->tables = new Collection();
    }

    /**
     * Register all required classes.
     */
    abstract public function init();

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
}
