<?php
/**
 * dbog .../test/dbog/TableTest.php
 */

namespace Test\Dbog;

use Test\CoreTestCase;

class TableTest extends CoreTestCase
{
    public function testTableName()
    {
        $this->assertEquals('test_table_template', $this->table->getTableName());
    }

    public function testTableRename()
    {
        $config = $this->table->getConfiguration();
        $config->setRenamedFrom('test_table');
        $this->assertEquals('test_table', $config->getRenamedFrom());
    }

    public function testEmptyConfig()
    {
        $config = $this->table->getConfiguration();
        $this->assertEquals('test_table_template', $config->getName());
        $this->assertNull($config->getRenamedFrom());
        $this->assertEmpty($config->getColumns());
        $this->assertFalse($config->getColumn('test_column'));
        $this->assertNull($config->getKeyPrimary());
        $this->assertEmpty($config->getKeysIndex());
        $this->assertEmpty($config->getKeysUnique());
        $this->assertEmpty($config->getTriggers());
    }
}
