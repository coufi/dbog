<?php
/**
 * dbog .../src/core/Table.php
 */

namespace Src\Core;


use Src\Core\Table\Config;
use Src\Exceptions\SyncerException;

abstract class Table extends Entity implements ValidableInterface
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
     * @todo Advanced validation implementation, if necessary check relation columns datatypes, check relation columns unique indexes, etc.
     */
    public function validate()
    {
        $config = $this->getConfiguration();

        // validate mappings
        foreach ($config->getRelationsMapping() as $mapping)
        {
            $mapping->validate();
        }

        // validate connections
        foreach ($config->getRelationsConnection() as $connection)
        {
            $connection->validate();
        }

        // validate extensions
        foreach ($config->getRelationsExtension() as $extension)
        {
            $extension->validate();
        }

        // validate mappeds
        foreach ($config->getRelationsMapped() as $mapped)
        {
            $mapped->validate();
        }

        // validate unique keys
        foreach ($config->getKeysUnique() as $unique)
        {
            $unique->validate();
        }

        // validate index keys
        foreach ($config->getKeysIndex() as $index)
        {
            $index->validate();
        }

        // validate pk
        if (!$config->getKeyPrimary())
        {
            throw new SyncerException("Missing primary key in table {$this->tableName}");
        }
    }
}