<?php
/**
 * dbog .../src/Config.php
 */

namespace Src;

use Src\Core\Schema;
use Src\Database\Instance;
use Src\Database\Server;

abstract class Config
{
    const DRIVER_MYSQL = 'mysql';

    /** @var Instance[] */
    protected $instances = [];

    /** @var Schema[] */
    protected $schemas = [];

    /**
     * @return Instance[]
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * @param Server $dbServer
     * @param Schema $schema
     * @param string $user
     * @param string $password
     * @param string $dbSchemaName Database schema name
     */
    protected function addInstance($dbServer, $schema, $user, $password, $dbSchemaName)
    {
        $this->instances[] = new Instance($dbServer, $schema, $user, $password, $dbSchemaName);
    }

    /**
     * @param string $driver See Config::DRIVER_* constants
     * @param string $dbHost
     * @param int|null $dbPort
     * @return Database\Server
     */
    protected function createDbServerConfig($driver, $dbHost, $dbPort = null)
    {
        return new Database\Server($driver, $dbHost, $dbPort);
    }

    /**
     * @param string $className
     * @return Schema
     */
    protected function createSchema($className)
    {
        /** @var Schema $className */
        if (!isset ($this->schemas[$className]))
        {
            $this->schemas[$className] = new $className();
            $this->schemas[$className]->init();
        }

        return $this->schemas[$className];
    }
}
