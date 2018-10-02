<?php
/**
 * dbog .../src/database/Server.php
 */

namespace Src\Database;

class Server
{
    const DEFAULT_PORT = 3306;

    /** @var string */
    protected $dbHost;
    /** @var int */
    protected $dbPort;
    /** @var string */
    protected $driver;

    /**
     * @param string $driver
     * @param string $dbHost
     * @param int $dbPort
     */
    public function __construct($driver, $dbHost, $dbPort = self::DEFAULT_PORT)
    {
        $this->driver = $driver;
        $this->dbHost = $dbHost;
        $this->dbPort = $dbPort;
    }

    /**
     * @return string
     */
    public function getDbHost()
    {
        return $this->dbHost;
    }

    /**
     * @return int
     */
    public function getDbPort()
    {
        return $this->dbPort;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }
}
