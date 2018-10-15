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
        return
"Validate database structure.
Database structure valid.
Syncing database structure.
USE `sync_test`;
SYNC: Switching to schema sync_test.
SET foreign_key_checks = 0;
SYNC: Removing table order.
DROP TABLE `order`;
SYNC: Removing table product_category.
DROP TABLE `product_category`;
SYNC: Removing table currency.
DROP TABLE `currency`;
SYNC: Removing table product.
DROP TABLE `product`;
SYNC: Removing table content.
DROP TABLE `content`;
SYNC: Removing table menu_item_product_category.
DROP TABLE `menu_item_product_category`;
SYNC: Removing table country.
DROP TABLE `country`;
SYNC: Removing table order_item.
DROP TABLE `order_item`;
SYNC: Removing table menu_item_content.
DROP TABLE `menu_item_content`;
SYNC: Removing table delivery_type.
DROP TABLE `delivery_type`;
SYNC: Removing table product_has_product_category.
DROP TABLE `product_has_product_category`;
SYNC: Removing table menu.
DROP TABLE `menu`;
SYNC: Removing table payment_type.
DROP TABLE `payment_type`;
SYNC: Removing table menu_item.
DROP TABLE `menu_item`;
SYNC: Removing table menu_item_url.
DROP TABLE `menu_item_url`;
SYNC: Removing view sales_overview.
DROP VIEW `sales_overview`;
SET foreign_key_checks = 1;
Finished successfully.
";
    }

    private function getExpectedCreateSchemaEntitiesOutput()
    {
        return
"USE `sync_test`;
SET foreign_key_checks = 0;
CREATE TABLE `content` (
`id_content` int unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(63) NOT NULL,
`uri` varchar(127) NOT NULL,
`content` text NULL,
CONSTRAINT `pk_content` PRIMARY KEY (`id_content`),
CONSTRAINT `uq_content_name` UNIQUE `uq_content_name` (`name`),
CONSTRAINT `uq_content_uri` UNIQUE `uq_content_uri` (`uri`)
) ENGINE 'innodb';
CREATE TABLE `country` (
`id_country` smallint unsigned NOT NULL AUTO_INCREMENT,
`code_2` char(2) NOT NULL,
`numeric_3` char(3) NOT NULL,
`name` varchar(31) NOT NULL,
CONSTRAINT `pk_country` PRIMARY KEY (`id_country`),
CONSTRAINT `uq_country_code_2` UNIQUE `uq_country_code_2` (`code_2`),
CONSTRAINT `uq_country_name` UNIQUE `uq_country_name` (`name`)
) ENGINE 'innodb';
CREATE TABLE `currency` (
`id_currency` smallint unsigned NOT NULL AUTO_INCREMENT,
`currency_short` varchar(3) NOT NULL,
`currency_symbol` varchar(5) NOT NULL,
CONSTRAINT `pk_currency` PRIMARY KEY (`id_currency`),
CONSTRAINT `uq_currency_currency_short` UNIQUE `uq_currency_currency_short` (`currency_short`),
CONSTRAINT `uq_currency_currency_symbol` UNIQUE `uq_currency_currency_symbol` (`currency_symbol`)
) ENGINE 'innodb';
CREATE TABLE `delivery_type` (
`id_delivery_type` tinyint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(31) NOT NULL,
CONSTRAINT `pk_delivery_type` PRIMARY KEY (`id_delivery_type`),
CONSTRAINT `uq_delivery_type_name` UNIQUE `uq_delivery_type_name` (`name`)
) ENGINE 'innodb';
CREATE TABLE `menu` (
`id_menu` smallint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(60) NOT NULL,
CONSTRAINT `pk_menu` PRIMARY KEY (`id_menu`),
CONSTRAINT `uq_menu_name` UNIQUE `uq_menu_name` (`name`)
) ENGINE 'innodb';
CREATE TABLE `menu_item` (
`id_menu_item` int unsigned NOT NULL AUTO_INCREMENT,
`menu_item` enum('PRODUCT_CATEGORY','CONTENT','URL') NOT NULL,
`id_menu` smallint unsigned NOT NULL,
`name` varchar(55) NOT NULL,
CONSTRAINT `pk_menu_item` PRIMARY KEY (`id_menu_item`),
CONSTRAINT `uq_menu_item_name` UNIQUE `uq_menu_item_name` (`name`),
INDEX `ix_menu_item_id_menu` (`id_menu`)
) ENGINE 'innodb';
CREATE TABLE `menu_item_content` (
`id_menu_item_content` smallint unsigned NOT NULL AUTO_INCREMENT,
`id_menu_item` int unsigned NOT NULL,
`id_content` int unsigned NOT NULL,
`name` varchar(55) NULL,
CONSTRAINT `pk_menu_item_content` PRIMARY KEY (`id_menu_item_content`),
CONSTRAINT `uq_menu_item_content_id_menu_item` UNIQUE `uq_menu_item_content_id_menu_item` (`id_menu_item`),
INDEX `ix_menu_item_content_id_menu_item` (`id_menu_item`),
INDEX `ix_menu_item_content_id_content` (`id_content`)
) ENGINE 'innodb';
CREATE TABLE `menu_item_product_category` (
`id_menu_item_product_category` smallint unsigned NOT NULL AUTO_INCREMENT,
`id_menu_item` int unsigned NOT NULL,
`id_product_category` smallint unsigned NOT NULL,
`name` varchar(55) NULL,
CONSTRAINT `pk_menu_item_product_category` PRIMARY KEY (`id_menu_item_product_category`),
CONSTRAINT `uq_menu_item_product_category_id_menu_item` UNIQUE `uq_menu_item_product_category_id_menu_item` (`id_menu_item`),
INDEX `ix_menu_item_product_category_id_menu_item` (`id_menu_item`),
INDEX `ix_menu_item_product_category_id_product_category` (`id_product_category`)
) ENGINE 'innodb';
CREATE TABLE `menu_item_url` (
`id_menu_item_url` smallint unsigned NOT NULL AUTO_INCREMENT,
`id_menu_item` int unsigned NOT NULL,
`url` varchar(511) NOT NULL,
`name` varchar(55) NULL,
CONSTRAINT `pk_menu_item_url` PRIMARY KEY (`id_menu_item_url`),
CONSTRAINT `uq_menu_item_url_id_menu_item` UNIQUE `uq_menu_item_url_id_menu_item` (`id_menu_item`),
INDEX `ix_menu_item_url_id_menu_item` (`id_menu_item`)
) ENGINE 'innodb';
CREATE TABLE `order` (
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
) ENGINE 'innodb';
CREATE TABLE `order_item` (
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
) ENGINE 'innodb';
CREATE TRIGGER `order_item_after_insert` AFTER INSERT ON `order_item` FOR EACH ROW
BEGIN
DECLARE `loc_total` DECIMAL(19,4) DEFAULT NULL;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = NEW.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = NEW.`id_order`;
END;
CREATE TRIGGER `order_item_after_update` AFTER UPDATE ON `order_item` FOR EACH ROW
BEGIN
DECLARE `loc_total` DECIMAL(19,4) DEFAULT NULL;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = NEW.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = NEW.`id_order`;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = OLD.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = OLD.`id_order`;
END;
CREATE TRIGGER `order_item_after_delete` AFTER DELETE ON `order_item` FOR EACH ROW
BEGIN
DECLARE `loc_total` DECIMAL(19,4) DEFAULT NULL;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = OLD.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = OLD.`id_order`;
END;
CREATE TABLE `payment_type` (
`id_payment_type` tinyint unsigned NOT NULL AUTO_INCREMENT,
`payment_phase` tinyint signed NULL,
`cash` tinyint(1) unsigned NOT NULL DEFAULT '0',
`name` varchar(31) NOT NULL,
CONSTRAINT `pk_payment_type` PRIMARY KEY (`id_payment_type`),
CONSTRAINT `uq_payment_type_name` UNIQUE `uq_payment_type_name` (`name`)
) ENGINE 'innodb';
CREATE TABLE `product` (
`id_product` int unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(63) NOT NULL,
`uri` varchar(127) NOT NULL,
CONSTRAINT `pk_product` PRIMARY KEY (`id_product`),
CONSTRAINT `uq_product_name` UNIQUE `uq_product_name` (`name`),
CONSTRAINT `uq_product_uri` UNIQUE `uq_product_uri` (`uri`)
) ENGINE 'innodb';
CREATE TABLE `product_category` (
`id_product_category` smallint unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(63) NOT NULL,
`uri` varchar(127) NOT NULL,
`content` text NULL,
CONSTRAINT `pk_product_category` PRIMARY KEY (`id_product_category`),
CONSTRAINT `uq_product_category_name` UNIQUE `uq_product_category_name` (`name`),
CONSTRAINT `uq_product_category_uri` UNIQUE `uq_product_category_uri` (`uri`)
) ENGINE 'innodb';
CREATE TABLE `product_has_product_category` (
`id_product_has_product_category` int unsigned NOT NULL AUTO_INCREMENT,
`id_product` int unsigned NOT NULL,
`id_product_category` smallint unsigned NOT NULL,
`priority` int unsigned NULL,
CONSTRAINT `pk_product_has_product_category` PRIMARY KEY (`id_product_has_product_category`),
CONSTRAINT `uq_product_has_product_category_id_product_id_product_category` UNIQUE `uq_product_has_product_category_id_product_id_product_category` (`id_product`, `id_product_category`),
INDEX `ix_product_has_product_category_id_product` (`id_product`),
INDEX `ix_product_has_product_category_id_product_category` (`id_product_category`)
) ENGINE 'innodb';
CREATE VIEW sales_overview AS select `order_item`.`id_product` AS `id_product`,sum(`order_item`.`quantity`) AS `sold_quantity` from `order_item` where `order_item`.`canceled` = 0 group by `order_item`.`id_product`;
ALTER TABLE `menu_item` ADD CONSTRAINT `fk_menu_item_id_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_content` FOREIGN KEY (`id_content`) REFERENCES `content` (`id_content`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_url` ADD CONSTRAINT `fk_menu_item_url_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_payment_type` FOREIGN KEY (`id_payment_type`) REFERENCES `payment_type` (`id_payment_type`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_delivery_type` FOREIGN KEY (`id_delivery_type`) REFERENCES `delivery_type` (`id_delivery_type`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id_currency`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_billing_row_6` FOREIGN KEY (`billing_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_delivery_row_6` FOREIGN KEY (`delivery_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_order` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION;
SET foreign_key_checks = 1;
";

    }

    private function getChangeDbSchemaCharsetOutput()
    {
        return
"USE `sync_test`;
ALTER SCHEMA sync_test CHARACTER SET 'ascii';
SET foreign_key_checks = 0;
SET foreign_key_checks = 1;
";
    }

    private function getChangeDbSchemaCollationOutput()
    {
        return
"USE `sync_test`;
ALTER SCHEMA sync_test COLLATE 'ascii_general_ci';
SET foreign_key_checks = 0;
ALTER TABLE `content` COLLATE 'ascii_general_ci';
ALTER TABLE `content` CHANGE `name` `name` varchar(63) NOT NULL  AFTER `id_content`;
ALTER TABLE `content` CHANGE `uri` `uri` varchar(127) NOT NULL  AFTER `name`;
ALTER TABLE `content` CHANGE `content` `content` text NULL  AFTER `uri`;
ALTER TABLE `country` COLLATE 'ascii_general_ci';
ALTER TABLE `country` CHANGE `code_2` `code_2` char(2) NOT NULL  AFTER `id_country`;
ALTER TABLE `country` CHANGE `numeric_3` `numeric_3` char(3) NOT NULL  AFTER `code_2`;
ALTER TABLE `country` CHANGE `name` `name` varchar(31) NOT NULL  AFTER `numeric_3`;
ALTER TABLE `currency` COLLATE 'ascii_general_ci';
ALTER TABLE `currency` CHANGE `currency_short` `currency_short` varchar(3) NOT NULL  AFTER `id_currency`;
ALTER TABLE `currency` CHANGE `currency_symbol` `currency_symbol` varchar(5) NOT NULL  AFTER `currency_short`;
ALTER TABLE `delivery_type` COLLATE 'ascii_general_ci';
ALTER TABLE `delivery_type` CHANGE `name` `name` varchar(31) NOT NULL  AFTER `id_delivery_type`;
ALTER TABLE `menu` COLLATE 'ascii_general_ci';
ALTER TABLE `menu` CHANGE `name` `name` varchar(60) NOT NULL  AFTER `id_menu`;
ALTER TABLE `menu_item` COLLATE 'ascii_general_ci';
ALTER TABLE `menu_item` CHANGE `menu_item` `menu_item` enum('PRODUCT_CATEGORY','CONTENT','URL') NOT NULL  AFTER `id_menu_item`;
ALTER TABLE `menu_item` CHANGE `name` `name` varchar(55) NOT NULL  AFTER `id_menu`;
ALTER TABLE `menu_item_content` COLLATE 'ascii_general_ci';
ALTER TABLE `menu_item_content` CHANGE `name` `name` varchar(55) NULL  AFTER `id_content`;
ALTER TABLE `menu_item_product_category` COLLATE 'ascii_general_ci';
ALTER TABLE `menu_item_product_category` CHANGE `name` `name` varchar(55) NULL  AFTER `id_product_category`;
ALTER TABLE `menu_item_url` COLLATE 'ascii_general_ci';
ALTER TABLE `menu_item_url` CHANGE `url` `url` varchar(511) NOT NULL  AFTER `id_menu_item`;
ALTER TABLE `menu_item_url` CHANGE `name` `name` varchar(55) NULL  AFTER `url`;
ALTER TABLE `order` COLLATE 'ascii_general_ci';
ALTER TABLE `order` CHANGE `first_name` `first_name` varchar(63) NULL  AFTER `id_order`;
ALTER TABLE `order` CHANGE `surname` `surname` varchar(31) NULL  AFTER `first_name`;
ALTER TABLE `order` CHANGE `phone` `phone` varchar(12) NULL  AFTER `id_currency`;
ALTER TABLE `order` CHANGE `e_mail` `e_mail` varchar(63) NULL  AFTER `phone`;
ALTER TABLE `order` CHANGE `institution` `institution` varchar(63) NULL  AFTER `e_mail`;
ALTER TABLE `order` CHANGE `in` `in` varchar(10) NULL  AFTER `institution`;
ALTER TABLE `order` CHANGE `vat` `vat` varchar(14) NULL  AFTER `in`;
ALTER TABLE `order` CHANGE `billing_row_1` `billing_row_1` varchar(63) NULL  AFTER `vat`;
ALTER TABLE `order` CHANGE `billing_row_2` `billing_row_2` varchar(63) NULL  AFTER `billing_row_1`;
ALTER TABLE `order` CHANGE `billing_row_3` `billing_row_3` varchar(127) NULL  AFTER `billing_row_2`;
ALTER TABLE `order` CHANGE `billing_row_4` `billing_row_4` varchar(127) NULL  AFTER `billing_row_3`;
ALTER TABLE `order` CHANGE `billing_row_5` `billing_row_5` varchar(10) NULL  AFTER `billing_row_4`;
ALTER TABLE `order` CHANGE `delivery_row_1` `delivery_row_1` varchar(63) NULL  AFTER `billing_row_6`;
ALTER TABLE `order` CHANGE `delivery_row_2` `delivery_row_2` varchar(63) NULL  AFTER `delivery_row_1`;
ALTER TABLE `order` CHANGE `delivery_row_3` `delivery_row_3` varchar(127) NULL  AFTER `delivery_row_2`;
ALTER TABLE `order` CHANGE `delivery_row_4` `delivery_row_4` varchar(127) NULL  AFTER `delivery_row_3`;
ALTER TABLE `order` CHANGE `delivery_row_5` `delivery_row_5` varchar(10) NULL  AFTER `delivery_row_4`;
ALTER TABLE `order` CHANGE `gender` `gender` enum('MAN','WOMAN','I_DONT_KNOW') NULL  AFTER `delivery_row_6`;
ALTER TABLE `order` CHANGE `hash` `hash` varchar(128) NULL  AFTER `gender`;
ALTER TABLE `order_item` COLLATE 'ascii_general_ci';
ALTER TABLE `order_item` CHANGE `note` `note` varchar(255) NULL  AFTER `canceled`;
ALTER TABLE `payment_type` COLLATE 'ascii_general_ci';
ALTER TABLE `payment_type` CHANGE `name` `name` varchar(31) NOT NULL  AFTER `cash`;
ALTER TABLE `product` COLLATE 'ascii_general_ci';
ALTER TABLE `product` CHANGE `name` `name` varchar(63) NOT NULL  AFTER `id_product`;
ALTER TABLE `product` CHANGE `uri` `uri` varchar(127) NOT NULL  AFTER `name`;
ALTER TABLE `product_category` COLLATE 'ascii_general_ci';
ALTER TABLE `product_category` CHANGE `name` `name` varchar(63) NOT NULL  AFTER `id_product_category`;
ALTER TABLE `product_category` CHANGE `uri` `uri` varchar(127) NOT NULL  AFTER `name`;
ALTER TABLE `product_category` CHANGE `content` `content` text NULL  AFTER `uri`;
ALTER TABLE `product_has_product_category` COLLATE 'ascii_general_ci';
SET foreign_key_checks = 1;
";
    }

    protected function getChangeSchemaEngineOutput()
    {
        return
"USE `sync_test`;
SET foreign_key_checks = 0;
ALTER TABLE `menu_item_content` DROP FOREIGN KEY fk_menu_item_content_id_content;
ALTER TABLE `content` ENGINE 'myisam';
ALTER TABLE `order` DROP FOREIGN KEY fk_order_billing_row_6;
ALTER TABLE `order` DROP FOREIGN KEY fk_order_delivery_row_6;
ALTER TABLE `country` ENGINE 'myisam';
ALTER TABLE `order` DROP FOREIGN KEY fk_order_id_currency;
ALTER TABLE `currency` ENGINE 'myisam';
ALTER TABLE `order` DROP FOREIGN KEY fk_order_id_delivery_type;
ALTER TABLE `delivery_type` ENGINE 'myisam';
ALTER TABLE `menu_item` DROP FOREIGN KEY fk_menu_item_id_menu;
ALTER TABLE `menu` ENGINE 'myisam';
ALTER TABLE `menu_item_content` DROP FOREIGN KEY fk_menu_item_content_id_menu_item;
ALTER TABLE `menu_item_product_category` DROP FOREIGN KEY fk_menu_item_product_category_id_menu_item;
ALTER TABLE `menu_item_url` DROP FOREIGN KEY fk_menu_item_url_id_menu_item;
ALTER TABLE `menu_item` ENGINE 'myisam';
ALTER TABLE `menu_item` ADD CONSTRAINT `fk_menu_item_id_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_content` ENGINE 'myisam';
ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_content` FOREIGN KEY (`id_content`) REFERENCES `content` (`id_content`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_product_category` DROP FOREIGN KEY fk_menu_item_product_category_id_product_category;
ALTER TABLE `menu_item_product_category` ENGINE 'myisam';
ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_url` ENGINE 'myisam';
ALTER TABLE `menu_item_url` ADD CONSTRAINT `fk_menu_item_url_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order_item` DROP FOREIGN KEY fk_order_item_id_order;
ALTER TABLE `order` DROP FOREIGN KEY fk_order_id_payment_type;
ALTER TABLE `order` ENGINE 'myisam';
ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_payment_type` FOREIGN KEY (`id_payment_type`) REFERENCES `payment_type` (`id_payment_type`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_delivery_type` FOREIGN KEY (`id_delivery_type`) REFERENCES `delivery_type` (`id_delivery_type`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id_currency`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_billing_row_6` FOREIGN KEY (`billing_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_delivery_row_6` FOREIGN KEY (`delivery_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order_item` DROP FOREIGN KEY fk_order_item_id_product;
ALTER TABLE `order_item` ENGINE 'myisam';
ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_order` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `payment_type` ENGINE 'myisam';
ALTER TABLE `product_has_product_category` DROP FOREIGN KEY fk_product_has_product_category_id_product;
ALTER TABLE `product` ENGINE 'myisam';
ALTER TABLE `product_has_product_category` DROP FOREIGN KEY fk_product_has_product_category_id_product_category;
ALTER TABLE `product_category` ENGINE 'myisam';
ALTER TABLE `product_has_product_category` ENGINE 'myisam';
ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item` ADD CONSTRAINT `fk_menu_item_id_menu` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_content` ADD CONSTRAINT `fk_menu_item_content_id_content` FOREIGN KEY (`id_content`) REFERENCES `content` (`id_content`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_product_category` ADD CONSTRAINT `fk_menu_item_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `menu_item_url` ADD CONSTRAINT `fk_menu_item_url_id_menu_item` FOREIGN KEY (`id_menu_item`) REFERENCES `menu_item` (`id_menu_item`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_payment_type` FOREIGN KEY (`id_payment_type`) REFERENCES `payment_type` (`id_payment_type`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_delivery_type` FOREIGN KEY (`id_delivery_type`) REFERENCES `delivery_type` (`id_delivery_type`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_id_currency` FOREIGN KEY (`id_currency`) REFERENCES `currency` (`id_currency`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_billing_row_6` FOREIGN KEY (`billing_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order` ADD CONSTRAINT `fk_order_delivery_row_6` FOREIGN KEY (`delivery_row_6`) REFERENCES `country` (`id_country`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_order` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `order_item` ADD CONSTRAINT `fk_order_item_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `product_has_product_category` ADD CONSTRAINT `fk_product_has_product_category_id_product_category` FOREIGN KEY (`id_product_category`) REFERENCES `product_category` (`id_product_category`) ON DELETE NO ACTION ON UPDATE NO ACTION;
SET foreign_key_checks = 1;
";

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
        $this->outputEndEquals($this->getExpectedCreateSchemaEntitiesOutput());
    }

    public function testChangeSchemaCharset()
    {
        $config = new ConfigTest();
        $schema = $config->getInstances()[0]->getSchema();
        $schema->setDbCharset(Schema::DB_CHARSET_ASCII);
        $this->outputBegin();
        $dbog = new Dbog($config, new LoggerPrint());
        $dbog->run(true, false);
        $this->outputEndEquals($this->getChangeDbSchemaCharsetOutput());
    }

    public function testChangeSchemaCollation()
    {
        $config = new ConfigTest();
        $schema = $config->getInstances()[0]->getSchema();
        $schema->setDbCollation(Schema::DB_COLLATION_ASCII_GENERAL_CI);
        $this->outputBegin();
        $dbog = new Dbog($config, new LoggerPrint());
        $dbog->run(true, false);
        $this->outputEndEquals($this->getChangeDbSchemaCollationOutput());
    }

    public function testChangeSchemaEngine()
    {
        $config = new ConfigTest();
        $schema = $config->getInstances()[0]->getSchema();
        $schema->setEngine(Schema::ENGINE_MYISAM);
        $this->outputBegin();
        $dbog = new Dbog($config, new LoggerPrint());
        $dbog->run(true, false);
        $this->outputEndEquals($this->getChangeSchemaEngineOutput());
    }
}
