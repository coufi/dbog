<?php
/**
 * dbog .../src/syncer/Runner.php
 */

namespace Src\Syncer;

use Src\Core\Schema;
use Src\Database\AdapterInterface;
use Src\Exceptions\SyncerException;
use Src\Logger;

class Runner
{
    /** @var AdapterInterface */
    protected $db;

    /** @var Schema */
    protected $schema;

    /** @var string */
    protected $dbSchemaName;

    /** @var bool */
    protected $dryRun = false;

    /** @var Logger */
    protected $logger;

    /**
     * @param AdapterInterface $db
     * @param Schema $schema
     * @param string $dbSchemaName
     */
    public function __construct($db, $schema, $dbSchemaName)
    {
        $this->db = $db;
        $this->schema = $schema;
        $this->dbSchemaName = $dbSchemaName;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $enabled bool
     */
    public function setDryRunMode($enabled)
    {
        $this->dryRun = $enabled;
    }

    /**
     * Get db adapter instance.
     * @return AdapterInterface
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Get database schema name.
     * @return string
     */
    public function getDbSchemaName()
    {
        return $this->dbSchemaName;
    }

    /**
     * Sync whole database structure.
     * @throws SyncerException
     */
    public function syncStructure()
    {
        $this->log("Validate database structure.");
        $this->schema->validate();
        $this->log("Database structure valid.");

        $this->log("Syncing database structure.");
        $this->processQuery("USE `$this->dbSchemaName`", true);
        $this->log("SYNC: Switching to schema $this->dbSchemaName.");

        $this->schema->sync($this);

        $this->log("Finished successfully.");
    }

    /**
     * Process query.
     * @param string $query SQL query string
     */
    public function processQuery($query)
    {
        $this->logger->logQuery($query . ';');

        if (!$this->dryRun)
        {
            $this->db->query($query);
        }
    }

    /**
     * Log info message.
     * @param string $message
     */
    public function log($message)
    {
        $this->logger->logMessage($message);
    }
}
