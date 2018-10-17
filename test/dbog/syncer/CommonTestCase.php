<?php
/**
 * dbog .../test/dbog/syncer/CommonTestCase.php
 */

namespace Test\Dbog\Syncer;

use Conf\ConfigTest;
use Demo\Schema;
use Src\Dbog;
use Src\LoggerPrint;

abstract class CommonTestCase extends RunnerTestCase
{
    /**  @var Schema */
    protected $schema;

    /**  @var ConfigTest */
    protected $config;

    protected function setUp()
    {
        $this->config = new ConfigTest();
        $this->schema = $this->config->getInstances()[0]->getSchema();

        parent::setUp();
    }

    protected function rundDbog()
    {
        $dbog = new Dbog($this->config, new LoggerPrint());
        $dbog->run(true, false);
    }
}
