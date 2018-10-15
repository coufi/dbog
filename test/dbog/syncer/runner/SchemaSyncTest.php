<?php
/**
 * dbog .../test/syncer/SchemaSyncTest.php
 */

namespace Test\Dbog\Syncer\Runner;


use Conf\ConfigTest;
use Src\Core\Schema;
use Src\Dbog;
use Src\LoggerPrint;
use Test\Dbog\Syncer\RunnerTestCase;
use Test\EmptySchema;


class SchemaSyncTest extends RunnerTestCase
{
    private function getExpectedDropSchemaEntitiesOutput()
    {
        $lines = [];
        $lines[] = 'Validate database structure.';
        $lines[] = 'Database structure valid.';
        $lines[] = 'Syncing database structure.';
        $lines[] = 'USE `sync_test`;';
        $lines[] = 'SYNC: Switching to schema sync_test.';
        $lines[] = 'SET foreign_key_checks = 0;';
        $lines[] = 'SYNC: Removing table order.';
        $lines[] = 'DROP TABLE `order`;';
        $lines[] = 'SYNC: Removing table product_category.';
        $lines[] = 'DROP TABLE `product_category`;';
        $lines[] = 'SYNC: Removing table currency.';
        $lines[] = 'DROP TABLE `currency`;';
        $lines[] = 'SYNC: Removing table product.';
        $lines[] = 'DROP TABLE `product`;';
        $lines[] = 'SYNC: Removing table content.';
        $lines[] = 'DROP TABLE `content`;';
        $lines[] = 'SYNC: Removing table menu_item_product_category.';
        $lines[] = 'DROP TABLE `menu_item_product_category`;';
        $lines[] = 'SYNC: Removing table country.';
        $lines[] = 'DROP TABLE `country`;';
        $lines[] = 'SYNC: Removing table order_item.';
        $lines[] = 'DROP TABLE `order_item`;';
        $lines[] = 'SYNC: Removing table menu_item_content.';
        $lines[] = 'DROP TABLE `menu_item_content`;';
        $lines[] = 'SYNC: Removing table delivery_type.';
        $lines[] = 'DROP TABLE `delivery_type`;';
        $lines[] = 'SYNC: Removing table product_has_product_category.';
        $lines[] = 'DROP TABLE `product_has_product_category`;';
        $lines[] = 'SYNC: Removing table menu.';
        $lines[] = 'DROP TABLE `menu`;';
        $lines[] = 'SYNC: Removing table payment_type.';
        $lines[] = 'DROP TABLE `payment_type`;';
        $lines[] = 'SYNC: Removing table menu_item.';
        $lines[] = 'DROP TABLE `menu_item`;';
        $lines[] = 'SYNC: Removing table menu_item_url.';
        $lines[] = 'DROP TABLE `menu_item_url`;';
        $lines[] = 'SYNC: Removing view sales_overview.';
        $lines[] = 'DROP VIEW `sales_overview`;';
        $lines[] = 'SET foreign_key_checks = 1;';
        $lines[] = 'Finished successfully.';

        return $lines;
    }

    private function getExpectedCreateSchemaEntitiesOutput()
    {
        $queries = [];
        $queries[] = 'USE `sync_test`';
        $queries[] = 'SET foreign_key_checks = 0';
        $queries[] = "CREATE TABLE `content` (
`id_content` int unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(63) NOT NULL,
`uri` varchar(127) NOT NULL,
`content` text NULL,
CONSTRAINT `pk_content` PRIMARY KEY (`id_content`),
CONSTRAINT `uq_content_name` UNIQUE `uq_content_name` (`name`),
CONSTRAINT `uq_content_uri` UNIQUE `uq_content_uri` (`uri`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `country` (
`id_country` smallint unsigned NOT NULL AUTO_INCREMENT,
`code_2` char(2) NOT NULL,
`numeric_3` char(3) NOT NULL,
`name` varchar(31) NOT NULL,
CONSTRAINT `pk_country` PRIMARY KEY (`id_country`),
CONSTRAINT `uq_country_code_2` UNIQUE `uq_country_code_2` (`code_2`),
CONSTRAINT `uq_country_name` UNIQUE `uq_country_name` (`name`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `currency` (
`id_currency` smallint unsigned NOT NULL AUTO_INCREMENT,
`currency_short` varchar(3) NOT NULL,
`currency_symbol` varchar(5) NOT NULL,
CONSTRAINT `pk_currency` PRIMARY KEY (`id_currency`),
CONSTRAINT `uq_currency_currency_short` UNIQUE `uq_currency_currency_short` (`currency_short`),
CONSTRAINT `uq_currency_currency_symbol` UNIQUE `uq_currency_currency_symbol` (`currency_symbol`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `delivery_type` (
`id_delivery_type` tinyint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(31) NOT NULL,
CONSTRAINT `pk_delivery_type` PRIMARY KEY (`id_delivery_type`),
CONSTRAINT `uq_delivery_type_name` UNIQUE `uq_delivery_type_name` (`name`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `menu` (
`id_menu` smallint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(60) NOT NULL,
CONSTRAINT `pk_menu` PRIMARY KEY (`id_menu`),
CONSTRAINT `uq_menu_name` UNIQUE `uq_menu_name` (`name`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `menu_item` (
`id_menu_item` int unsigned NOT NULL AUTO_INCREMENT,
`menu_item` enum('PRODUCT_CATEGORY','CONTENT','URL') NOT NULL,
`id_menu` smallint unsigned NOT NULL,
`name` varchar(55) NOT NULL,
CONSTRAINT `pk_menu_item` PRIMARY KEY (`id_menu_item`),
CONSTRAINT `uq_menu_item_name` UNIQUE `uq_menu_item_name` (`name`),
INDEX `ix_menu_item_id_menu` (`id_menu`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `menu_item_content` (
`id_menu_item_content` smallint unsigned NOT NULL AUTO_INCREMENT,
`id_menu_item` int unsigned NOT NULL,
`id_content` int unsigned NOT NULL,
`name` varchar(55) NULL,
CONSTRAINT `pk_menu_item_content` PRIMARY KEY (`id_menu_item_content`),
CONSTRAINT `uq_menu_item_content_id_menu_item` UNIQUE `uq_menu_item_content_id_menu_item` (`id_menu_item`),
INDEX `ix_menu_item_content_id_menu_item` (`id_menu_item`),
INDEX `ix_menu_item_content_id_content` (`id_content`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `menu_item_product_category` (
`id_menu_item_product_category` smallint unsigned NOT NULL AUTO_INCREMENT,
`id_menu_item` int unsigned NOT NULL,
`id_product_category` smallint unsigned NOT NULL,
`name` varchar(55) NULL,
CONSTRAINT `pk_menu_item_product_category` PRIMARY KEY (`id_menu_item_product_category`),
CONSTRAINT `uq_menu_item_product_category_id_menu_item` UNIQUE `uq_menu_item_product_category_id_menu_item` (`id_menu_item`),
INDEX `ix_menu_item_product_category_id_menu_item` (`id_menu_item`),
INDEX `ix_menu_item_product_category_id_product_category` (`id_product_category`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `menu_item_url` (
`id_menu_item_url` smallint unsigned NOT NULL AUTO_INCREMENT,
`id_menu_item` int unsigned NOT NULL,
`url` varchar(511) NOT NULL,
`name` varchar(55) NULL,
CONSTRAINT `pk_menu_item_url` PRIMARY KEY (`id_menu_item_url`),
CONSTRAINT `uq_menu_item_url_id_menu_item` UNIQUE `uq_menu_item_url_id_menu_item` (`id_menu_item`),
INDEX `ix_menu_item_url_id_menu_item` (`id_menu_item`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `order` (
`id_order` int unsigned NOT NULL AUTO_INCREMENT,
`first_name` varchar(63) NULL,
`surname` varchar(31) NULL,
`timestamp` datetime NULL,
`id_payment_type` tinyint unsigned NULL,
`id_delivery_type` tinyint unsigned NULL,
`confirmed` tinyint(1) unsigned NOT NULL DEFAULT '0',
`canceled` tinyint(1) unsigned NOT NULL DEFAULT '0',
`total` decimal(19,4) unsigned NOT NULL DEFAULT '0.0000',
`id_currency` smallint unsigned NULL,
`phone` varchar(12) NULL,
`e_mail` varchar(63) NULL,
`institution` varchar(63) NULL,
`in` varchar(10) NULL,
`vat` varchar(14) NULL,
`billing_row_1` varchar(63) NULL,
`billing_row_2` varchar(63) NULL,
`billing_row_3` varchar(127) NULL,
`billing_row_4` varchar(127) NULL,
`billing_row_5` varchar(10) NULL,
`billing_row_6` smallint unsigned NULL,
`delivery_row_1` varchar(63) NULL,
`delivery_row_2` varchar(63) NULL,
`delivery_row_3` varchar(127) NULL,
`delivery_row_4` varchar(127) NULL,
`delivery_row_5` varchar(10) NULL,
`delivery_row_6` smallint unsigned NULL,
`gender` enum('MAN','WOMAN','I_DONT_KNOW') NULL,
`hash` varchar(128) NULL,
CONSTRAINT `pk_order` PRIMARY KEY (`id_order`),
INDEX `ix_order_id_payment_type` (`id_payment_type`),
INDEX `ix_order_id_delivery_type` (`id_delivery_type`),
INDEX `ix_order_id_currency` (`id_currency`),
INDEX `ix_order_billing_row_6` (`billing_row_6`),
INDEX `ix_order_delivery_row_6` (`delivery_row_6`),
INDEX `ix_order_hash` (`hash`(12)),
INDEX `ix_order_timestamp` (`timestamp`),
INDEX `ix_order_e_mail` (`e_mail`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `order_item` (
`id_order_item` int unsigned NOT NULL AUTO_INCREMENT,
`id_order` int unsigned NOT NULL,
`id_product` int unsigned NOT NULL,
`timestamp` datetime NOT NULL,
`quantity` smallint unsigned NOT NULL DEFAULT '1',
`price` decimal(19,4) NULL,
`confirmed` tinyint(1) unsigned NOT NULL DEFAULT '0',
`canceled` tinyint(1) unsigned NOT NULL DEFAULT '0',
`note` varchar(255) NULL,
CONSTRAINT `pk_order_item` PRIMARY KEY (`id_order_item`),
INDEX `ix_order_item_id_order` (`id_order`),
INDEX `ix_order_item_id_product` (`id_product`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TRIGGER `order_item_after_insert` AFTER INSERT ON `order_item` FOR EACH ROW
BEGIN
DECLARE `loc_total` DECIMAL(19,4) DEFAULT NULL;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = NEW.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = NEW.`id_order`;
END";
        $queries[] = "CREATE TRIGGER `order_item_after_update` AFTER UPDATE ON `order_item` FOR EACH ROW
BEGIN
DECLARE `loc_total` DECIMAL(19,4) DEFAULT NULL;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = NEW.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = NEW.`id_order`;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = OLD.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = OLD.`id_order`;
END";
        $queries[] = "CREATE TRIGGER `order_item_after_delete` AFTER DELETE ON `order_item` FOR EACH ROW
BEGIN
DECLARE `loc_total` DECIMAL(19,4) DEFAULT NULL;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = OLD.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = OLD.`id_order`;
END";
        $queries[] = "CREATE TABLE `payment_type` (
`id_payment_type` tinyint unsigned NOT NULL AUTO_INCREMENT,
`payment_phase` tinyint signed NULL,
`cash` tinyint(1) unsigned NOT NULL DEFAULT '0',
`name` varchar(31) NOT NULL,
CONSTRAINT `pk_payment_type` PRIMARY KEY (`id_payment_type`),
CONSTRAINT `uq_payment_type_name` UNIQUE `uq_payment_type_name` (`name`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `product` (
`id_product` int unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(63) NOT NULL,
`uri` varchar(127) NOT NULL,
CONSTRAINT `pk_product` PRIMARY KEY (`id_product`),
CONSTRAINT `uq_product_name` UNIQUE `uq_product_name` (`name`),
CONSTRAINT `uq_product_uri` UNIQUE `uq_product_uri` (`uri`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `product_category` (
`id_product_category` smallint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(63) NOT NULL,
`uri` varchar(127) NOT NULL,
`content` text NULL,
CONSTRAINT `pk_product_category` PRIMARY KEY (`id_product_category`),
CONSTRAINT `uq_product_category_name` UNIQUE `uq_product_category_name` (`name`),
CONSTRAINT `uq_product_category_uri` UNIQUE `uq_product_category_uri` (`uri`)
) ENGINE 'innodb'";
        $queries[] = "CREATE TABLE `product_has_product_category` (
`id_product_has_product_category` int unsigned NOT NULL AUTO_INCREMENT,
`id_product` int unsigned NOT NULL,
`id_product_category` smallint unsigned NOT NULL,
`priority` int unsigned NULL,
CONSTRAINT `pk_product_has_product_category` PRIMARY KEY (`id_product_has_product_category`),
CONSTRAINT `uq_product_has_product_category_id_product_id_product_category` UNIQUE `uq_product_has_product_category_id_product_id_product_category` (`id_product`, `id_product_category`),
INDEX `ix_product_has_product_category_id_product` (`id_product`),
INDEX `ix_product_has_product_category_id_product_category` (`id_product_category`)
) ENGINE 'innodb'";
        $queries[] = "CREATE VIEW sales_overview AS select `order_item`.`id_product` AS `id_product`,sum(`order_item`.`quantity`) AS `sold_quantity` from `order_item` where `order_item`.`canceled` = 0 group by `order_item`.`id_product`";
        $queries[] = "ALTER TABLE `menu_item` ADD CONSTRAINT `fk_menu_item_id_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_content` FOREIGN KEY (`id_content`) REFERENCES `content` (`id_content`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_url` ADD CONSTRAINT `fk_menu_item_url_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_payment_type` FOREIGN KEY (`id_payment_type`) REFERENCES `payment_type` (`id_payment_type`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_delivery_type` FOREIGN KEY (`id_delivery_type`) REFERENCES `delivery_type` (`id_delivery_type`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id_currency`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_billing_row_6` FOREIGN KEY (`billing_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_delivery_row_6` FOREIGN KEY (`delivery_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_order` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "SET foreign_key_checks = 1";

        return $queries;
    }

    private function getChangeDbSchemaCharsetOutput()
    {
        $lines = [];
        $lines[] = 'USE `sync_test`';
        $lines[] = 'ALTER SCHEMA sync_test CHARACTER SET \'ascii\'';
        $lines[] = 'SET foreign_key_checks = 0';
        $lines[] = 'SET foreign_key_checks = 1';

        return $lines;
    }

    private function getChangeDbSchemaCollationOutput()
    {
        $queries = [];
        $queries[] = "USE `sync_test`";
        $queries[] = "ALTER SCHEMA sync_test COLLATE 'ascii_general_ci'";
        $queries[] = "SET foreign_key_checks = 0";
        $queries[] = "ALTER TABLE `content` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `content` CHANGE `name` `name` varchar(63) NOT NULL  AFTER `id_content`";
        $queries[] = "ALTER TABLE `content` CHANGE `uri` `uri` varchar(127) NOT NULL  AFTER `name`";
        $queries[] = "ALTER TABLE `content` CHANGE `content` `content` text NULL  AFTER `uri`";
        $queries[] = "ALTER TABLE `country` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `country` CHANGE `code_2` `code_2` char(2) NOT NULL  AFTER `id_country`";
        $queries[] = "ALTER TABLE `country` CHANGE `numeric_3` `numeric_3` char(3) NOT NULL  AFTER `code_2`";
        $queries[] = "ALTER TABLE `country` CHANGE `name` `name` varchar(31) NOT NULL  AFTER `numeric_3`";
        $queries[] = "ALTER TABLE `currency` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `currency` CHANGE `currency_short` `currency_short` varchar(3) NOT NULL  AFTER `id_currency`";
        $queries[] = "ALTER TABLE `currency` CHANGE `currency_symbol` `currency_symbol` varchar(5) NOT NULL  AFTER `currency_short`";
        $queries[] = "ALTER TABLE `delivery_type` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `delivery_type` CHANGE `name` `name` varchar(31) NOT NULL  AFTER `id_delivery_type`";
        $queries[] = "ALTER TABLE `menu` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `menu` CHANGE `name` `name` varchar(60) NOT NULL  AFTER `id_menu`";
        $queries[] = "ALTER TABLE `menu_item` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `menu_item` CHANGE `menu_item` `menu_item` enum('PRODUCT_CATEGORY','CONTENT','URL') NOT NULL  AFTER `id_menu_item`";
        $queries[] = "ALTER TABLE `menu_item` CHANGE `name` `name` varchar(55) NOT NULL  AFTER `id_menu`";
        $queries[] = "ALTER TABLE `menu_item_content` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `menu_item_content` CHANGE `name` `name` varchar(55) NULL  AFTER `id_content`";
        $queries[] = "ALTER TABLE `menu_item_product_category` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `menu_item_product_category` CHANGE `name` `name` varchar(55) NULL  AFTER `id_product_category`";
        $queries[] = "ALTER TABLE `menu_item_url` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `menu_item_url` CHANGE `url` `url` varchar(511) NOT NULL  AFTER `id_menu_item`";
        $queries[] = "ALTER TABLE `menu_item_url` CHANGE `name` `name` varchar(55) NULL  AFTER `url`";
        $queries[] = "ALTER TABLE `order` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `order` CHANGE `first_name` `first_name` varchar(63) NULL  AFTER `id_order`";
        $queries[] = "ALTER TABLE `order` CHANGE `surname` `surname` varchar(31) NULL  AFTER `first_name`";
        $queries[] = "ALTER TABLE `order` CHANGE `phone` `phone` varchar(12) NULL  AFTER `id_currency`";
        $queries[] = "ALTER TABLE `order` CHANGE `e_mail` `e_mail` varchar(63) NULL  AFTER `phone`";
        $queries[] = "ALTER TABLE `order` CHANGE `institution` `institution` varchar(63) NULL  AFTER `e_mail`";
        $queries[] = "ALTER TABLE `order` CHANGE `in` `in` varchar(10) NULL  AFTER `institution`";
        $queries[] = "ALTER TABLE `order` CHANGE `vat` `vat` varchar(14) NULL  AFTER `in`";
        $queries[] = "ALTER TABLE `order` CHANGE `billing_row_1` `billing_row_1` varchar(63) NULL  AFTER `vat`";
        $queries[] = "ALTER TABLE `order` CHANGE `billing_row_2` `billing_row_2` varchar(63) NULL  AFTER `billing_row_1`";
        $queries[] = "ALTER TABLE `order` CHANGE `billing_row_3` `billing_row_3` varchar(127) NULL  AFTER `billing_row_2`";
        $queries[] = "ALTER TABLE `order` CHANGE `billing_row_4` `billing_row_4` varchar(127) NULL  AFTER `billing_row_3`";
        $queries[] = "ALTER TABLE `order` CHANGE `billing_row_5` `billing_row_5` varchar(10) NULL  AFTER `billing_row_4`";
        $queries[] = "ALTER TABLE `order` CHANGE `delivery_row_1` `delivery_row_1` varchar(63) NULL  AFTER `billing_row_6`";
        $queries[] = "ALTER TABLE `order` CHANGE `delivery_row_2` `delivery_row_2` varchar(63) NULL  AFTER `delivery_row_1`";
        $queries[] = "ALTER TABLE `order` CHANGE `delivery_row_3` `delivery_row_3` varchar(127) NULL  AFTER `delivery_row_2`";
        $queries[] = "ALTER TABLE `order` CHANGE `delivery_row_4` `delivery_row_4` varchar(127) NULL  AFTER `delivery_row_3`";
        $queries[] = "ALTER TABLE `order` CHANGE `delivery_row_5` `delivery_row_5` varchar(10) NULL  AFTER `delivery_row_4`";
        $queries[] = "ALTER TABLE `order` CHANGE `gender` `gender` enum('MAN','WOMAN','I_DONT_KNOW') NULL  AFTER `delivery_row_6`";
        $queries[] = "ALTER TABLE `order` CHANGE `hash` `hash` varchar(128) NULL  AFTER `gender`";
        $queries[] = "ALTER TABLE `order_item` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `order_item` CHANGE `note` `note` varchar(255) NULL  AFTER `canceled`";
        $queries[] = "ALTER TABLE `payment_type` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `payment_type` CHANGE `name` `name` varchar(31) NOT NULL  AFTER `cash`";
        $queries[] = "ALTER TABLE `product` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `product` CHANGE `name` `name` varchar(63) NOT NULL  AFTER `id_product`";
        $queries[] = "ALTER TABLE `product` CHANGE `uri` `uri` varchar(127) NOT NULL  AFTER `name`";
        $queries[] = "ALTER TABLE `product_category` COLLATE 'ascii_general_ci'";
        $queries[] = "ALTER TABLE `product_category` CHANGE `name` `name` varchar(63) NOT NULL  AFTER `id_product_category`";
        $queries[] = "ALTER TABLE `product_category` CHANGE `uri` `uri` varchar(127) NOT NULL  AFTER `name`";
        $queries[] = "ALTER TABLE `product_category` CHANGE `content` `content` text NULL  AFTER `uri`";
        $queries[] = "ALTER TABLE `product_has_product_category` COLLATE 'ascii_general_ci'";
        $queries[] = "SET foreign_key_checks = 1";

        return $queries;
    }

    protected function getChangeSchemaEngineOutput()
    {
        $queries = [];
        $queries[] = "USE `sync_test`";
        $queries[] = "SET foreign_key_checks = 0";
        $queries[] = "ALTER TABLE `menu_item_content` DROP FOREIGN KEY fk_menu_item_content_id_content";
        $queries[] = "ALTER TABLE `content` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `order` DROP FOREIGN KEY fk_order_billing_row_6";
        $queries[] = "ALTER TABLE `order` DROP FOREIGN KEY fk_order_delivery_row_6";
        $queries[] = "ALTER TABLE `country` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `order` DROP FOREIGN KEY fk_order_id_currency";
        $queries[] = "ALTER TABLE `currency` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `order` DROP FOREIGN KEY fk_order_id_delivery_type";
        $queries[] = "ALTER TABLE `delivery_type` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `menu_item` DROP FOREIGN KEY fk_menu_item_id_menu";
        $queries[] = "ALTER TABLE `menu` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `menu_item_content` DROP FOREIGN KEY fk_menu_item_content_id_menu_item";
        $queries[] = "ALTER TABLE `menu_item_product_category` DROP FOREIGN KEY fk_menu_item_product_category_id_menu_item";
        $queries[] = "ALTER TABLE `menu_item_url` DROP FOREIGN KEY fk_menu_item_url_id_menu_item";
        $queries[] = "ALTER TABLE `menu_item` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `menu_item` ADD CONSTRAINT `fk_menu_item_id_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_content` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_content` FOREIGN KEY (`id_content`) REFERENCES `content` (`id_content`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_product_category` DROP FOREIGN KEY fk_menu_item_product_category_id_product_category";
        $queries[] = "ALTER TABLE `menu_item_product_category` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_url` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `menu_item_url` ADD CONSTRAINT `fk_menu_item_url_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order_item` DROP FOREIGN KEY fk_order_item_id_order";
        $queries[] = "ALTER TABLE `order` DROP FOREIGN KEY fk_order_id_payment_type";
        $queries[] = "ALTER TABLE `order` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_payment_type` FOREIGN KEY (`id_payment_type`) REFERENCES `payment_type` (`id_payment_type`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_delivery_type` FOREIGN KEY (`id_delivery_type`) REFERENCES `delivery_type` (`id_delivery_type`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id_currency`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_billing_row_6` FOREIGN KEY (`billing_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_delivery_row_6` FOREIGN KEY (`delivery_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order_item` DROP FOREIGN KEY fk_order_item_id_product";
        $queries[] = "ALTER TABLE `order_item` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_order` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `payment_type` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `product_has_product_category` DROP FOREIGN KEY fk_product_has_product_category_id_product";
        $queries[] = "ALTER TABLE `product` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `product_has_product_category` DROP FOREIGN KEY fk_product_has_product_category_id_product_category";
        $queries[] = "ALTER TABLE `product_category` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `product_has_product_category` ENGINE 'myisam'";
        $queries[] = "ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item` ADD CONSTRAINT `fk_menu_item_id_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_content` FOREIGN KEY (`id_content`) REFERENCES `content` (`id_content`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `menu_item_url` ADD CONSTRAINT `fk_menu_item_url_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_payment_type` FOREIGN KEY (`id_payment_type`) REFERENCES `payment_type` (`id_payment_type`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_delivery_type` FOREIGN KEY (`id_delivery_type`) REFERENCES `delivery_type` (`id_delivery_type`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id_currency`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_billing_row_6` FOREIGN KEY (`billing_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order` ADD CONSTRAINT `fk_order_delivery_row_6` FOREIGN KEY (`delivery_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_order` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION";
        $queries[] = "SET foreign_key_checks = 1";

        return $queries;
    }

    public function testDropAndCreateDb()
    {
        // DROP DATABASE SCHEMA ENTITIES

        // substitute an empty schema into config object
        $config = new ConfigTest();
        $instance = $config->getInstances()[0];
        $instance->setSchema(new EmptySchema());
        $this->outputBegin();
        $dbog = new Dbog($config, new LoggerPrint());
        $dbog->run(true, true);
        $this->outputEndEquals($this->getExpectedDropSchemaEntitiesOutput());

        // CREATE DATABASE SCHEMA ENTITIES
        $this->outputBegin();
        $dbog = new Dbog(new ConfigTest(), new LoggerPrint());
        $dbog->run(true, false);
        $this->outputEndEquals($this->getExpectedCreateSchemaEntitiesOutput(), true);
    }

    public function testChangeSchemaCharset()
    {
        $config = new ConfigTest();
        $schema = $config->getInstances()[0]->getSchema();
        $schema->setDbCharset(Schema::DB_CHARSET_ASCII);
        $this->outputBegin();
        $dbog = new Dbog($config, new LoggerPrint());
        $dbog->run(true, false);
        $this->outputEndEquals($this->getChangeDbSchemaCharsetOutput(), true);
    }

    public function testChangeSchemaCollation()
    {
        $config = new ConfigTest();
        $schema = $config->getInstances()[0]->getSchema();
        $schema->setDbCollation(Schema::DB_COLLATION_ASCII_GENERAL_CI);
        $this->outputBegin();
        $dbog = new Dbog($config, new LoggerPrint());
        $dbog->run(true, false);
        $this->outputEndEquals($this->getChangeDbSchemaCollationOutput(), true);
    }

    public function testChangeSchemaEngine()
    {
        $config = new ConfigTest();
        $schema = $config->getInstances()[0]->getSchema();
        $schema->setEngine(Schema::ENGINE_MYISAM);
        $this->outputBegin();
        $dbog = new Dbog($config, new LoggerPrint());
        $dbog->run(true, false);
        $this->outputEndEquals($this->getChangeSchemaEngineOutput(), true);
    }
}
