<?php
/**
 * dbog .../test/dbog/SchemaTest.php
 */

namespace Test\Dbog;

use \PHPUnit\Framework\TestCase AS TestCase;
use Src\Core\Schema;
use Test\TestTableTemplate;

class SchemaTest extends TestCase
{
    /** @var Schema */
    protected $schema;

    protected function setUp()
    {
        $this->schema = new \Demo\Schema();
    }

    public function testCollection()
    {
        //empty uninitialized collection
        $this->assertEmpty($this->schema->getTableNames());
        $this->assertEmpty($this->schema->getViewNames());

        $this->schema->init();
        $this->assertNotEmpty($this->schema->getTableNames());
        $this->assertNotEmpty($this->schema->getTableNames());

        $this->schema->addTable(TestTableTemplate::class);
        $this->assertTrue($this->schema->hasTable(TestTableTemplate::getLabel()));
    }

    public function testInstanceLoading()
    {
        $this->schema->init();
        $this->schema->addTable(TestTableTemplate::class);

        $this->assertInstanceOf(TestTableTemplate::class, $this->schema->getTable(TestTableTemplate::getLabel()));
    }
}
