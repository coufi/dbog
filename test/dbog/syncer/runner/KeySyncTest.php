<?php
/**
 * dbog .../test/syncer/KeySyncTest.php
 */

namespace Test\Dbog\Syncer\Runner;


use Test\Dbog\Syncer\CommonTestCase;

class KeySyncTest extends CommonTestCase
{

    private function getAddedIndexOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `order_item` ADD INDEX `ix_order_item_timestamp` (`timestamp`)';
        $lines[] = 'ALTER TABLE `order_item` ADD INDEX `custom_key_name` (`quantity`)';
        $lines[] = 'ALTER TABLE `order_item` ADD INDEX `ix_order_item_note` (`note`(10))';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    private function getChangedIndexOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `order` DROP INDEX `ix_order_hash`';
        $lines[] = 'ALTER TABLE `order` ADD INDEX `ix_order_hash` (`hash`(16))';
        $lines[] = 'ALTER TABLE `order` ADD INDEX `order_timestamp_index` (`timestamp`)';
        $lines[] = 'ALTER TABLE `order` DROP INDEX `ix_order_timestamp`';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    private function getAddedUqOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `order_item` ADD CONSTRAINT `uq_order_item_timestamp` UNIQUE `uq_order_item_timestamp` (`timestamp`)';
        $lines[] = 'ALTER TABLE `order_item` ADD CONSTRAINT `custom_key_name` UNIQUE `custom_key_name` (`quantity`)';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    private function getChangedUqOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `product` ADD CONSTRAINT `custom_key_name` UNIQUE `custom_key_name` (`name`)';
        $lines[] = 'ALTER TABLE `product` DROP INDEX `uq_product_name`';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    private function getChangedPkOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'ALTER TABLE `order_item` DROP FOREIGN KEY fk_order_item_id_order';
        $lines[] = 'ALTER TABLE `order` CHANGE `id_order` `id_order` smallint unsigned NOT NULL AUTO_INCREMENT FIRST';
        $lines[] = 'ALTER TABLE `order_item` CHANGE `id_order` `id_order` smallint unsigned NOT NULL AFTER `id_order_item`';
        $lines[] = 'ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_order` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`) ON DELETE NO ACTION ON UPDATE NO ACTION';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    public function testAddedIndex()
    {
        $tableConfig = $this->schema->getTable('order_item')->getConfiguration();
        $tableConfig->addKeyIndex(['timestamp']);
        $tableConfig->addKeyIndex(['quantity'])->setCustomKeyName('custom_key_name');
        $tableConfig->addKeyIndex(['note'])->setPrefixLength('note', 10);

        $this->outputBegin();
        $this->rundDbog(true);
        $this->outputEndEquals($this->getAddedIndexOutput(), true);

    }

    public function testChangedIndex()
    {
        $tableConfig = $this->schema->getTable('order')->getConfiguration();
        $tableConfig->getKeysIndex()[5]->setPrefixLength('hash', 16);
        $tableConfig->getKeysIndex()[6]->setCustomKeyName('order_timestamp_index');

        $this->outputBegin();
        $this->rundDbog(true);
        $this->outputEndEquals($this->getChangedIndexOutput(), true);

    }

    public function testAddedUq()
    {
        $tableConfig = $this->schema->getTable('order_item')->getConfiguration();
        $tableConfig->addKeyUnique(['timestamp']);
        $tableConfig->addKeyUnique(['quantity'])->setCustomKeyName('custom_key_name');

        $this->outputBegin();
        $this->rundDbog(true);
        $this->outputEndEquals($this->getAddedUqOutput(), true);
    }

    public function testChangedUq()
    {
        $tableConfig = $this->schema->getTable('product')->getConfiguration();
        $tableConfig->getKeysUnique()[0]->setCustomKeyName('custom_key_name');

        $this->outputBegin();
        $this->rundDbog(true);
        $this->outputEndEquals($this->getChangedUqOutput(), true);
    }

    public function testChangedPk()
    {
        $tableConfig = $this->schema->getTable('order')->getConfiguration();
        $tableConfig->addPrimary()->setSmallIntUnsigned();

        $this->outputBegin();
        $this->rundDbog();
        $this->outputEndEquals($this->getChangedPkOutput(), true);
    }
}
