<?php
/**
 * dbog .../test/CoreTestCase.php
 */

namespace Test;

use \PHPUnit\Framework\TestCase AS TestCase;

abstract class CoreTestCase extends TestCase
{
    /**  @var TestTableTemplate */
    protected $table;

    protected function setUp()
    {
        $this->table = new TestTableTemplate();
    }
}
