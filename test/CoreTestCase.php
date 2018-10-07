<?php
/**
 * dbog .../test/CoreTestCase.php
 */

namespace Test;

use \PHPUnit\Framework\TestCase AS TestCase;
use Src\Core\TableContainer;

abstract class CoreTestCase extends TestCase
{
    /**  @var TestTableTemplate */
    protected $table;

    /**  @var TableContainer */
    protected $container;

    protected function setUp()
    {
        $this->container = new TestTableContainer();
        $this->container->init();
        $this->table = $this->container->get('test_table_template');
    }
}
