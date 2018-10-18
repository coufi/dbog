<?php
/**
 * dbog .../test/syncer/ColumnSyncTest.php
 */

namespace Test\Dbog\Syncer\Runner;

use Src\Core\Datatype\DtBigint;
use Test\Dbog\Syncer\CommonTestCase;

class ColumnSyncTest extends CommonTestCase
{
    private function getAddedColumnOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `product` ADD `description` varchar(127) NOT NULL AFTER `uri`';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    private function getChangedColumnNullabilityOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `product_category` CHANGE `name` `name` varchar(63) NULL AFTER `id_product_category`';
        $lines[] = 'ALTER TABLE `product_category` CHANGE `content` `content` text NOT NULL AFTER `uri`';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    private function getChangedColumnDefaultValueOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `order` CHANGE `total` `total` decimal(19,4) unsigned NOT NULL DEFAULT \'1.0000\' AFTER `canceled`';
        $lines[] = 'ALTER TABLE `order` CHANGE `e_mail` `e_mail` varchar(63) NULL DEFAULT \'@\' AFTER `phone`';
        $lines[] = 'ALTER TABLE `order_item` CHANGE `quantity` `quantity` smallint unsigned NOT NULL DEFAULT \'10\' AFTER `timestamp`';
        $lines[] = 'ALTER TABLE `order_item` CHANGE `confirmed` `confirmed` tinyint(1) unsigned NOT NULL DEFAULT \'1\' AFTER `price`';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    private function getChangedColumnDatatypeOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `order` CHANGE `first_name` `first_name` bigint signed NULL AFTER `id_order`';
        $lines[] = 'ALTER TABLE `order` CHANGE `timestamp` `timestamp` date NULL AFTER `surname`';
        $lines[] = 'ALTER TABLE `order` CHANGE `total` `total` decimal(10,0) NOT NULL DEFAULT \'0.0000\' AFTER `canceled`';
        $lines[] = 'ALTER TABLE `order` CHANGE `phone` `phone` int signed NULL AFTER `id_currency`';
        $lines[] = 'ALTER TABLE `order` CHANGE `e_mail` `e_mail` mediumint signed NULL AFTER `phone`';
        $lines[] = 'ALTER TABLE `order` CHANGE `institution` `institution` mediumint unsigned NULL AFTER `e_mail`';
        $lines[] = "ALTER TABLE `order` CHANGE `in` `in` set('val1','val2') NULL AFTER `institution`";
        $lines[] = 'ALTER TABLE `order` CHANGE `vat` `vat` smallint signed NULL AFTER `in`';
        $lines[] = 'ALTER TABLE `order` CHANGE `billing_row_1` `billing_row_1` mediumtext NULL AFTER `vat`';
        $lines[] = 'ALTER TABLE `order` CHANGE `billing_row_2` `billing_row_2` time NULL AFTER `billing_row_1`';
        $lines[] = 'ALTER TABLE `order` CHANGE `billing_row_3` `billing_row_3` year NULL AFTER `billing_row_2`';
        $lines[] = 'ALTER TABLE `order` CHANGE `billing_row_4` `billing_row_4` tinyint signed NULL AFTER `billing_row_3`';
        $lines[] = "ALTER TABLE `order` CHANGE `gender` `gender` enum('MAN','WOMAN','UNDEFINED') NULL AFTER `delivery_row_6`";
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    private function getChangedColumnNameOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `product_category` CHANGE `content` `test` text NULL AFTER `uri`';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    private function getRemovedColumnOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `country` CHANGE `name` `name` varchar(31) NOT NULL AFTER `code_2`';
        $lines[] = 'ALTER TABLE `country` DROP `numeric_3`';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    public function testAddedColumn()
    {
        $tableConfig = $this->schema->getTable('product')->getConfiguration();
        $tableConfig->addColumn('description')->setString();

        $this->outputBegin();
        $this->rundDbog();
        $this->outputEndEquals($this->getAddedColumnOutput(), true);
    }

    public function testChangedColumnNullability()
    {
        $tableConfig = $this->schema->getTable('product_category')->getConfiguration();
        $tableConfig->getColumn('name')->setNull();
        $tableConfig->getColumn('content')->setNull(false);

        $this->outputBegin();
        $this->rundDbog(true);
        $this->outputEndEquals($this->getChangedColumnNullabilityOutput(), true);
    }

    public function testChangedColumnDefaultValue()
    {
        $tableConfig = $this->schema->getTable('order_item')->getConfiguration();
        $tableConfig->getColumn('quantity')->setDefault('10');
        $tableConfig->getColumn('confirmed')->setDefault(true);

        $tableConfig = $this->schema->getTable('order')->getConfiguration();
        $tableConfig->getColumn('total')->setDefault('1.0000');
        $tableConfig->getColumn('e_mail')->setDefault('@');

        $this->outputBegin();
        $this->rundDbog(true);
        $this->outputEndEquals($this->getChangedColumnDefaultValueOutput(), true);
    }

    public function testChangedColumnDatatype()
    {
        $tableConfig = $this->schema->getTable('order')->getConfiguration();
        // set bigint signed directly
        $dt = new DtBigint();
        $dt->setUnsigned(false);
        $tableConfig->getColumn('first_name')->setDatatype($dt);

        // set date
        $tableConfig->getColumn('timestamp')->setDate();

        // set decimal sig
        $tableConfig->getColumn('total')->setDecimalSigned();

        // set int sig
        $tableConfig->getColumn('phone')->setIntSigned();

        // set mediumint sig
        $tableConfig->getColumn('e_mail')->setMediumIntSigned();

        // set mediumint unsig
        $tableConfig->getColumn('institution')->setMediumIntUnsigned();

        // set set
        $tableConfig->getColumn('in')->setSet(['val1', 'val2']);

        // set smallint sig
        $tableConfig->getColumn('vat')->setSmallIntSigned();

        // set textmedium
        $tableConfig->getColumn('billing_row_1')->setTextMedium();

        // set time
        $tableConfig->getColumn('billing_row_2')->setTime();

        // set year
        $tableConfig->getColumn('billing_row_3')->setYear();

        // set tinyint signed
        $tableConfig->getColumn('billing_row_4')->setTinyIntSigned();

        // set changed enum
        $tableConfig->getColumn('gender')->setEnum(['MAN', 'WOMAN', 'UNDEFINED']);


        $this->outputBegin();
        $this->rundDbog(true);
        $this->outputEndEquals($this->getChangedColumnDatatypeOutput(), true);
    }

    public function testChangedColumnName()
    {
        // override with similar class
        $this->schema->addTable(\Test\Schema\Table\ProductCategory::class);

        $this->outputBegin();
        $this->rundDbog(true);
        $this->outputEndEquals($this->getChangedColumnNameOutput(), true);
    }

    public function testRemovedColumn()
    {
        // override with similar class
        $this->schema->addTable(\Test\Schema\Table\Country::class);

        $tableConfig = $this->schema->getTable('order')->getConfiguration();
        // should be ignored as no change in db detected
        $tableConfig->getColumn('first_name')->setRenamedFrom('name');

        $this->outputBegin();
        $this->rundDbog();
        $this->outputEndEquals($this->getRemovedColumnOutput(), true);
    }
}
