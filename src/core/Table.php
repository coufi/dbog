<?php
/**
 * dbog .../src/core/Table.php
 */

namespace Src\Core;


use Src\Core\Table\Config;
use Src\Exceptions\SyncerException;

abstract class Table extends Entity
{
    /** @var string */
    protected $tableName;

    /** @var Collection */
    protected $tableContainer;

    /** @var Config */
    protected $configCache;

    /**
     * @param Collection $tableContainer
     */
    public function __construct($tableContainer)
    {
        $this->tableName = self::getLabel();
        $this->tableContainer = $tableContainer;
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
     * Get table container.
     * @return Collection
     */
    public function getTableContainer()
    {
        return $this->tableContainer;
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
        return new Config($this->tableName, $this->tableContainer);
    }

    /**
     * Validate table's keys and relations
     * @throws SyncerException
     */
    public function validate()
    {
        $config = $this->getConfiguration();

        // validate mappings
        foreach ($config->getRelationsMapping() as $mapping)
        {
            $mapping->validate();
        }
    }
}