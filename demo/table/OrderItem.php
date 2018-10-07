<?php
/**
 * dbog .../demo/table/OrderItem.php
 */

namespace Demo\Table;


use Src\Core\Trigger;

class OrderItem extends \Src\Core\Table
{
    const COLUMN_ORDER_ID = 'id_order';
    const COLUMN_PRODUCT_ID = 'id_productg';
    const COLUMN_TIMESTAMP = 'timestamp';
    const COLUMN_QUANTITY = 'quantity';
    const COLUMN_PRICE = 'price';
    const COLUMN_CONFIRMED = 'confirmed';
    const COLUMN_CANCELED = 'canceled';
    const COLUMN_NOTE = 'note';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()->setIntUnsigned();
        $config->addColumn(self::COLUMN_ORDER_ID)              ->setFK();
        $config->addColumn(self::COLUMN_PRODUCT_ID)            ->setFK('product');
        $config->addColumn(self::COLUMN_TIMESTAMP)             ->setDatetime();
        $config->addColumn(self::COLUMN_QUANTITY)              ->setSmallIntUnsigned()  ->setDefault('1');
        $config->addColumn(self::COLUMN_PRICE)                 ->setDecimalSigned(19, 4)    ->setNull();
        $config->addColumn(self::COLUMN_CONFIRMED)             ->setBool()              ->setDefault(false);
        $config->addColumn(self::COLUMN_CANCELED)              ->setBool()              ->setDefault(false);
        $config->addColumn(self::COLUMN_NOTE)                  ->setString(255)  ->setNull();

        $config->addTrigger(
            Trigger::TIME_AFTER,
            Trigger::ACTION_INSERT,
            'DECLARE `loc_total` DECIMAL(19,4) DEFAULT NULL;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = NEW.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = NEW.`id_order`;'
        );

        $config->addTrigger(
            Trigger::TIME_AFTER,
            Trigger::ACTION_UPDATE,
            'DECLARE `loc_total` DECIMAL(19,4) DEFAULT NULL;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = NEW.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = NEW.`id_order`;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = OLD.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = OLD.`id_order`;'
        );

        $config->addTrigger(
            Trigger::TIME_AFTER,
            Trigger::ACTION_DELETE, 'DECLARE `loc_total` DECIMAL(19,4) DEFAULT NULL;
SELECT IFNULL(SUM(`oi`.`quantity` * `oi`.`price`), 0) AS `total` INTO `loc_total` FROM `order_item` AS `oi` WHERE `oi`.`id_order` = OLD.`id_order` AND `oi`.`confirmed` AND NOT `oi`.`canceled`;
UPDATE `order` SET `total` = `loc_total` WHERE `id_order` = OLD.`id_order`;'
        );

        return $config;
    }
}