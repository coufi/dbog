<?php
/**
 * dbog .../src/Config.php
 */

namespace Src;

use Src\Core\TableContainer;
use Src\Database\Instance;
use Src\Database\Server;

abstract class Config
{
    const DRIVER_MYSQL = 'mysql';

    /** @var Instance[] */
    protected $instances = [];

    /**
     * @return Instance[]
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * @param Server $dbServer
     * @param string $schema
     * @param string $user
     * @param string $password
     * @param TableContainer $tableContainer
     */
    protected function addInstance($dbServer, $schema, $user, $password, $tableContainer)
    {
        $this->instances[] = new Instance($dbServer, $schema, $user, $password, $tableContainer);
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
}
