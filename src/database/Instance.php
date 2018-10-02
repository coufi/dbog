<?php
/**
 * dbog .../src/database/Instance.php
 */

namespace Src\Database;

use Src\Core\TableContainer;

class Instance
{
    /** @var Server */
    protected $dbServer;
    /** @var string */
    protected $schema;
    /** @var string */
    protected $user;
    /** @var string */
    protected $password;
    /** @var TableContainer */
    protected $tableContainer;

    /**
     * @param Server $dbServer
     * @param string $schema Database schema name
     * @param string $user Database user
     * @param string $password Database password
     * @param TableContainer $tableContainer
     */
    public function __construct($dbServer, $schema, $user, $password, $tableContainer)
    {
        $this->dbServer = $dbServer;
        $this->schema = $schema;
        $this->user = $user;
        $this->password = $password;
        $this->tableContainer = $tableContainer;
    }

    /**
     * @return Server
     */
    public function getDbServer()
    {
        return $this->dbServer;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return TableContainer
     */
    public function getTableContainer()
    {
        return $this->tableContainer;
    }
}
