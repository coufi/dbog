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

    /**
     * @param array|string $expectedOutput
     * @param bool $queriesOnly Whether output contains only SQL query statements
     */
    protected function outputEndEquals($expectedOutput, $queriesOnly = false)
    {
        $output = ob_get_clean();

        // add support for processing an array of lines
        if (is_array($expectedOutput))
        {
            if ($queriesOnly)
            {
                $output = explode(";\t\n", trim($output, ";\t\n"));
            }
            else
            {
                $output = explode("\t\n", trim($output, "\t\n"));
            }
        }

        $this->assertEquals($expectedOutput, $output);
    }
}
