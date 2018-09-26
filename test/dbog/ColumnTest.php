<?php
/**
 * dbog .../test/dbog/ColumnTest.php
 */

namespace Test\Dbog;

use Test\CoreTestCase;

class ColumnTest extends CoreTestCase
{
    public function testAddedColumn()
    {
        $config = $this->table->getConfiguration();
        $config->addColumn('column')->setNull();
        $config->addColumn('another_column')->setDefault(1);
        $this->assertCount(2, $config->getColumns());
        $this->assertEquals('column', array_keys($config->getColumns())[0]);
    }

    public function testRenamedColumn()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('column')->setRenamedFrom('original_name')->setNull();

        $this->assertEquals('column', $column->getName());
        $this->assertEquals('original_name', $column->getRenamedFrom());
    }

    public function testColumnDefaults()
    {
        $config = $this->table->getConfiguration();
        $config->addColumn('column')->setNull();
        $config->addColumn('another_column')->setDefault(1);
        $config->addColumn('bool_column_true')->setDefault(true);
        $config->addColumn('bool_column_false')->setDefault(false);
        $this->assertCount(4, $config->getColumns());

        $this->assertTrue($config->getColumn('column')->isNull());
        $this->assertFalse($config->getColumn('another_column')->isNull());
        $this->assertNull($config->getColumn('column')->getDefault());
        $this->assertSame(1, $config->getColumn('another_column')->getDefault());
        $this->assertSame(1, $config->getColumn('bool_column_true')->getDefault());
        $this->assertSame(0, $config->getColumn('bool_column_false')->getDefault());
    }

    public function testTinyIntSigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('id_column')->setTinyIntSigned();

        $this->assertEquals('tinyint signed', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(3, $column->getDatatype()->getSqlPrecision());
    }

    public function testTinyIntUnsigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('id_column')->setTinyIntUnsigned();

        $this->assertEquals('tinyint unsigned', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(3, $column->getDatatype()->getSqlPrecision());
    }

    public function testSmallIntSigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('id_column')->setSmallIntSigned();

        $this->assertEquals('smallint signed', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(5, $column->getDatatype()->getSqlPrecision());
    }

    public function testSmallIntUnsigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('id_column')->setSmallIntUnsigned();

        $this->assertEquals('smallint unsigned', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(5, $column->getDatatype()->getSqlPrecision());
    }

    public function testMediumIntSigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('id_column')->setMediumIntSigned();

        $this->assertEquals('mediumint signed', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(7, $column->getDatatype()->getSqlPrecision());
    }

    public function testMediumIntUnsigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('id_column')->setMediumIntUnsigned();

        $this->assertEquals('mediumint unsigned', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(7, $column->getDatatype()->getSqlPrecision());
    }

    public function testIntSigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('id_column')->setIntSigned();

        $this->assertEquals('int signed', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(10, $column->getDatatype()->getSqlPrecision());
    }

    public function testIntUnsigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('id_column')->setIntUnsigned();

        $this->assertEquals('int unsigned', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(10, $column->getDatatype()->getSqlPrecision());
    }

    public function testBigIntSigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('id_column')->setBigIntSigned();

        $this->assertEquals('bigint signed', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(20, $column->getDatatype()->getSqlPrecision());
    }

    public function testBigIntUnsigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('id_column')->setBigIntUnsigned();

        $this->assertEquals('bigint unsigned', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(20, $column->getDatatype()->getSqlPrecision());
    }

    public function testDecimalSigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('value')->setDecimalSigned();

        $this->assertEquals('decimal(10,0)', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(10, $column->getDatatype()->getSqlPrecision());
        $this->assertEquals(0, $column->getDatatype()->getSqlScale());
    }

    public function testDecimalUnsigned()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('value')->setDecimalUnsigned(19,4);

        $this->assertEquals('decimal(19,4) unsigned', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(19, $column->getDatatype()->getSqlPrecision());
        $this->assertEquals(4, $column->getDatatype()->getSqlScale());
    }

    public function testString()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('label')->setString(255);

        $this->assertEquals('varchar(255)', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(255, $column->getDatatype()->getSqlMaxLength());
    }


    public function testChar()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('abbreviation')->setChar(4);

        $this->assertEquals('char(4)', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(4, $column->getDatatype()->getSqlMaxLength());
    }

    public function testBool()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('checked')->setBool();

        $this->assertEquals('tinyint(1) unsigned', $column->getDatatype()->getSqlDefinition());
        $this->assertEquals(3, $column->getDatatype()->getSqlPrecision());
    }

    public function testYear()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('year')->setYear();

        $this->assertEquals('year', $column->getDatatype()->getSqlDefinition());
    }

    public function testDate()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('date')->setDate();

        $this->assertEquals('date', $column->getDatatype()->getSqlDefinition());
    }

    public function testDateTime()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('timestamp')->setDatetime();

        $this->assertEquals('datetime', $column->getDatatype()->getSqlDefinition());
    }

    public function testTime()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('time')->setTime();

        $this->assertEquals('time', $column->getDatatype()->getSqlDefinition());
    }

    public function testText()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('content')->setText();

        $this->assertEquals('text', $column->getDatatype()->getSqlDefinition());
    }

    public function testTextMedium()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('content')->setTextMedium();

        $this->assertEquals('mediumtext', $column->getDatatype()->getSqlDefinition());
    }

    public function testEnum()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('options')->setEnum(['OPTION_1', 'OPTION_2']);

        $this->assertEquals('enum(\'OPTION_1\',\'OPTION_2\')', $column->getDatatype()->getSqlDefinition());
    }

    public function testSet()
    {
        $config = $this->table->getConfiguration();
        $column = $config->addColumn('list')->setSet(['VAL_1', 'VAL_2']);

        $this->assertEquals('set(\'VAL_1\',\'VAL_2\')', $column->getDatatype()->getSqlDefinition());
    }
}
