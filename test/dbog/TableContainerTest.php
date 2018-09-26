<?php
/**
 * dbog .../test/dbog/TableContainerTest.php
 */

namespace Test\Dbog;

use \PHPUnit\Framework\TestCase AS TestCase;
use Src\Core\TableContainer;
use Test\TestTableTemplate;

class TableContainerTest extends TestCase
{
    /** @var TableContainer */
    protected $container;

    protected function setUp()
    {
        $this->container = new \Demo\TableContainer();
    }

    public function testCollection()
    {
        //empty uninitialized collection
        $this->assertEmpty($this->container->getClassNames());

        $this->container->init();
        $this->assertNotEmpty($this->container->getClassNames());

        $this->container->add(TestTableTemplate::class);
        $this->assertTrue($this->container->has(TestTableTemplate::class));
    }

    public function testInstanceLoading()
    {
        $this->container->init();
        $this->container->add(TestTableTemplate::class);

        $this->assertInstanceOf(TestTableTemplate::class, $this->container->get(TestTableTemplate::class));
    }
}