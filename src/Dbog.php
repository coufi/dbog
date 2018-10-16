<?php
/**
 * dbog .../src/Dbog.php
 */

namespace Src;


use Src\Database\AdapterPDO;
use Src\Database\Instance;
use Src\Exceptions\SyncerException;
use Src\Syncer\Runner;

class Dbog
{
    /** @var Logger */
    protected $logger;

    /** @var Config */
    protected $config;

    /**
     * @param Logger|null $logger
     * @param Config $config
     */
    public function __construct($config, $logger = null)
    {
        if (!$logger)
        {
            $logger = new LoggerVoid();
        }

        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Run synchronization.
     * @param bool $outputQueries Whether query logging is required
     * @param bool $verbose Whether logging of runtime messages is required
     * @param bool $dryRun Whether is required dry run without database changes
     */
    public function run($outputQueries = true, $verbose = true, $dryRun = false)
    {
        $this->logger->enableLogMessages($verbose);
        $this->logger->enableQueriesOutput($outputQueries);

        try
        {
            foreach ($this->config->getInstances() as $instance)
            {
                $runner = $this->createSyncerRunner($instance, $dryRun);
                $runner->syncStructure();
            }
        }
        catch (SyncerException $exception)
        {
            $this->logger->logMessage('Syncer error: ' . $exception->getMessage());
        }
        catch (\Exception $exception)
        {
            $this->logger->logMessage('Connection error: ' . $exception->getMessage());
        }

    }

    /**
     * Create new syncer runner instance.
     * @param Instance $instance
     * @param $dryRun bool
     * @return Runner
     * @throws \Exception
     */
    protected function createSyncerRunner($instance, $dryRun)
    {
        // TODO: Should not be hardcoded, refactor after first successful tests
        $db = new AdapterPDO();
        $db->connect($instance);

        $runner = new Runner($db, $instance->getSchema(), $instance->getDbSchemaName());
        $runner->setLogger($this->logger);
        $runner->setDryRunMode($dryRun);

        return $runner;
    }

    /**
     * Print documentation on screen.
     */
    public function printUsage()
    {
        echo <<<DOC
Usage:
------

./dbog

Parameters
----------
--output-queries                           - executed SQL queries are logged if specified (OPTIONAL)
--dry-run                                  - SQL queries are NOT executed if specified (OPTIONAL)
--verbose                                  - more detailed log output if specified (OPTIONAL)
--help                                     - shows this help 

DOC;
    }
}
