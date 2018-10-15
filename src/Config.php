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
     * @param string $user
     * @param string $password
     * @param string $dbSchemaName Database schema name
     * @return Instance
     */
    protected function addInstance($dbServer, $user, $password, $dbSchemaName)
    {
        $instance = new Instance($dbServer, $user, $password, $dbSchemaName);
        $this->instances[] = $instance;

        return $instance;
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
