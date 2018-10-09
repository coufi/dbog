<?php
/**
 * dbog .../test/dbog/ValidationTest.php
 */

namespace Test\Dbog;

use Demo\Schema;
use Demo\Table\OrderItem;
use \PHPUnit\Framework\TestCase AS TestCase;
use Src\Exceptions\SyncerException;

class ValidationTest extends TestCase
{
    /** @var Schema */
    protected $schema;

    protected function setUp()
    {
        $this->schema = new \Demo\Schema();
        $this->schema->init();
    }

    /**
     * @param callable $callback
     * @param string $message
     */
    protected function assertExceptionMessage($callback, $message)
    {
        $result = null;
        try
        {
            $callback();
        }
        catch (\Exception $exception)
        {
            $result = $exception->getMessage();
        }
        $this->assertEquals($result, $message);
    }

    public function testMissingPkException()
    {
        // added table does not have PK
        $this->schema->addTable(\Test\TestTableTemplate::class);

        $this->expectException(SyncerException::class);
        $this->schema->validate();
    }

    public function testMissingPkExceptionMessage()
    {
        // added table does not have PK
        $this->schema->addTable(\Test\TestTableTemplate::class);

        $this->assertExceptionMessage([$this->schema, 'validate'], 'Missing primary key in table test_table_template');
    }

    public function testWrongMappingReferenceException()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addColumn('new')->setFK('wrong_table');

        $this->expectException(SyncerException::class);
        $this->schema->validate();
    }

    public function testWrongMappingReferenceExceptionMessage()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addColumn('new')->setFK('wrong_table');

        $this->assertExceptionMessage([$this->schema, 'validate'], 'Mapping reference from table order_item to wrong_table not found');
    }

    public function testWrongMappingColumnException()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationMapping('product', ['wrong_column']);

        $this->expectException(SyncerException::class);
        $this->schema->validate();
    }

    public function testWrongMappingColumnExceptionMessage()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationMapping('product', ['wrong_column']);

        $this->assertExceptionMessage([$this->schema, 'validate'], 'Mapping column wrong_column not found in table order_item');
    }

    public function testWrongMappingTargetColumnException()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationMapping('product', ['id_product'], ['wrong_column']);

        $this->expectException(SyncerException::class);
        $this->schema->validate();
    }

    public function testWrongMappingTargetColumnExceptionMessage()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationMapping('product', ['id_product'], ['wrong_column']);

        $this->assertExceptionMessage([$this->schema, 'validate'], 'Mapping target wrong_column not found in mapping from table order_item to table product');
    }

    public function testWrongUniqueColumnException()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addKeyUnique(['wrong_column']);

        $this->expectException(SyncerException::class);
        $this->schema->validate();
    }

    public function testWrongUniqueColumnExceptionMessage()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addKeyUnique(['wrong_column']);

        $this->assertExceptionMessage([$this->schema, 'validate'], 'Unique column wrong_column not found in table order_item');
    }

    public function testWrongIndexColumnException()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addKeyIndex(['wrong_column']);

        $this->expectException(SyncerException::class);
        $this->schema->validate();
    }

    public function testWrongIndexColumnExceptionMessage()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addKeyIndex(['wrong_column']);

        $this->assertExceptionMessage([$this->schema, 'validate'], 'Indexed column wrong_column not found in table order_item');
    }

    public function testWrongMappedReferenceException()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationMapped('wrong_table');

        $this->expectException(SyncerException::class);
        $this->schema->validate();
    }

    public function testWrongMappedReferenceExceptionMessage()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationMapped('wrong_table');

        $this->assertExceptionMessage([$this->schema, 'validate'], 'Mapped table from order_item to wrong_table not found');
    }

    public function testWrongExtensionReferenceException()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationExtension('wrong_table');

        $this->expectException(SyncerException::class);
        $this->schema->validate();
    }

    public function testWrongExtensionReferenceExceptionMessage()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationExtension('wrong_table');

        $this->assertExceptionMessage([$this->schema, 'validate'], 'Extension table from order_item to wrong_table not found');
    }

    public function testWrongConnectionReferenceException()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationConnection('wrong_table', 'wrong_connecting_table');

        $this->expectException(SyncerException::class);
        $this->schema->validate();
    }

    public function testWrongConnectionReferenceExceptionMessage()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationConnection('wrong_table', 'wrong_connecting_table');

        $this->assertExceptionMessage([$this->schema, 'validate'], 'Connection reference from table order_item to wrong_table not found');
    }

    public function testWrongConnectingReferenceException()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationConnection('product', 'wrong_connecting_table');

        $this->expectException(SyncerException::class);
        $this->schema->validate();
    }

    public function testWrongConnectingReferenceExceptionMessage()
    {
        $config = $this->schema->getTable(OrderItem::getLabel())->getConfiguration();
        $config->addRelationConnection('product', 'wrong_connecting_table');

        $this->assertExceptionMessage([$this->schema, 'validate'], 'Connecting table wrong_connecting_table defined in order_item table not found');
    }
}
