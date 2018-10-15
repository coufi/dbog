<?php
/**
 * dbog .../test/CoreTestCase.php
 */

namespace Test;

use \PHPUnit\Framework\TestCase AS TestCase;
use Src\Core\Schema;

abstract class CoreTestCase extends TestCase
{
    /**  @var TestTableTemplate */
    protected $table;

    /**  @var Schema */
    protected $schema;

    protected function setUp()
    {
        $this->schema = new TestSchema();
        $this->schema->init();
        $this->table = $this->schema->getTable('test_table_template');
    }
}
