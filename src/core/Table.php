<?php
/**
 * dbog .../src/core/Table.php
 */

namespace Src\Core;


use Src\Core\Table\Config;

abstract class Table
{
    /** @var string */
    protected $tableName;

    /** @var Config */
    protected $configCache;

    public function __construct()
    {
        // convert camelcase class name to snake case table name
        $className = (new \ReflectionClass($this))->getShortName();
        $this->tableName = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $className)), '_');
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
     * Inicialize full configuration for table.
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
        return new Config($this->tableName);
    }
}