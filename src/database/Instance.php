<?php
/**
 * dbog .../src/database/Instance.php
 */

namespace Src\Database;

use Src\Core\Schema;

class Instance
{
    /** @var Server */
    protected $dbServer;
    /** @var Schema */
    protected $schema;
    /** @var string */
    protected $user;
    /** @var string */
    protected $password;
    /** @var string */
    protected $dbSchemaName;

    /**
     * @param Server $dbServer
     * @param string $user Database user
     * @param string $password Database password
     * @param string $dbSchemaName Database schema name
     */
    public function __construct($dbServer, $user, $password, $dbSchemaName)
    {
        $this->dbServer = $dbServer;
        $this->user = $user;
        $this->password = $password;
        $this->dbSchemaName = $dbSchemaName;
    }

    /**
     * @param Schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return Server
     */
    public function getDbServer()
    {
        return $this->dbServer;
    }

    /**
     * @return null|Schema
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
     * @return string
     */
    public function getDbSchemaName()
    {
        return $this->dbSchemaName;
    }
}
