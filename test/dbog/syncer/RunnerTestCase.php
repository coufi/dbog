<?php
/**
 * dbog .../test/dbog/syncer/RunnerTestCase.php
 */

namespace Test\Dbog\Syncer;

use Conf\ConfigTest;
use \PHPUnit\Framework\TestCase AS TestCase;
use Src\Dbog;

abstract class RunnerTestCase extends TestCase
{
    /** @var Dbog */
    protected $defaultDbog;

    protected function setUp()
    {
        if (!$this->defaultDbog)
        {
            // used for db synchronizing before each test, avoid logging
            $this->defaultDbog = new Dbog(new ConfigTest());
        }

        $this->syncDb();
    }

    protected function syncDb()
    {
        $this->defaultDbog->run(false, false);
    }

    protected function outputBegin()
    {
        ob_start();
    }

    protected function outputEndEquals($expectedOutput)
    {
        $output = ob_get_clean();
        $this->assertEquals($output, $expectedOutput);
    }
}
