<?php
/**
 * dbog .../demo/table/Order.php
 */

namespace Demo\Table;


class Order extends \Src\Core\Table
{
    const COLUMN_FIRST_NAME         = 'first_name';
    const COLUMN_SURNAME            = 'surname';
    const COLUMN_TIMESTAMP          = 'timestamp';
    const COLUMN_ID_PAYMENT_TYPE    = 'id_payment_type';
    const COLUMN_ID_DELIVERY_TYPE   = 'id_delivery_type';
    const COLUMN_CONFIRMED          = 'confirmed';
    const COLUMN_CANCELED           = 'canceled';
    const COLUMN_TOTAL              = 'total';
    const COLUMN_ID_CURRENCY        = 'id_currency';
    const COLUMN_PHONE              = 'phone';
    const COLUMN_E_MAIL             = 'e_mail';
    const COLUMN_INSTITUTION        = 'institution';
    const COLUMN_IN                 = 'in';
    const COLUMN_VAT                = 'vat';
    const COLUMN_BILLING_ROW_1      = 'billing_row_1';
    const COLUMN_BILLING_ROW_2      = 'billing_row_2';
    const COLUMN_BILLING_ROW_3      = 'billing_row_3';
    const COLUMN_BILLING_ROW_4      = 'billing_row_4';
    const COLUMN_BILLING_ROW_5      = 'billing_row_5';
    const COLUMN_BILLING_ROW_6      = 'billing_row_6';
    const COLUMN_DELIVERY_ROW_1     = 'delivery_row_1';
    const COLUMN_DELIVERY_ROW_2     = 'delivery_row_2';
    const COLUMN_DELIVERY_ROW_3     = 'delivery_row_3';
    const COLUMN_DELIVERY_ROW_4     = 'delivery_row_4';
    const COLUMN_DELIVERY_ROW_5     = 'delivery_row_5';
    const COLUMN_DELIVERY_ROW_6     = 'delivery_row_6';
    const COLUMN_GENDER             = 'gender';
    const COLUMN_HASH               = 'hash';

    const TABLE_COUNTRY     = 'country';
    const TABLE_ORDER_ITEM  = 'order_item';

    const GENDER_MAN         = 'MAN';
    const GENDER_WOMAN       = 'WOMAN';
    const GENDER_UNKNOWN     = 'I_DONT_KNOW';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $internalNames = [
            self::GENDER_MAN,
            self::GENDER_WOMAN,
            self::GENDER_UNKNOWN
        ];

        $config = $this->createConfig();
        $config->addPrimary()->setIntUnsigned();
        $config->addColumn(self::COLUMN_FIRST_NAME)           ->setString(63)           ->setNull();
        $config->addColumn(self::COLUMN_SURNAME)              ->setString(31)           ->setNull();
        $config->addColumn(self::COLUMN_TIMESTAMP)            ->setDatetime()                  ->setNull();
        $config->addColumn(self::COLUMN_ID_PAYMENT_TYPE)      ->setFK()                        ->setNull();
        $config->addColumn(self::COLUMN_ID_DELIVERY_TYPE)     ->setFK()                        ->setNull();
        $config->addColumn(self::COLUMN_CONFIRMED)            ->setBool()                      ->setDefault(false);
        $config->addColumn(self::COLUMN_CANCELED)             ->setBool()                      ->setDefault(false);
        $config->addColumn(self::COLUMN_TOTAL)                ->setDecimalUnsigned(19, 4)->setDefault('0.0000');
        $config->addColumn(self::COLUMN_ID_CURRENCY)          ->setFK()                        ->setNull();
        $config->addColumn(self::COLUMN_PHONE)                ->setString(12)           ->setNull();
        $config->addColumn(self::COLUMN_E_MAIL)               ->setString(63)           ->setNull();
        $config->addColumn(self::COLUMN_INSTITUTION)          ->setString(63)           ->setNull();
        $config->addColumn(self::COLUMN_IN)                   ->setString(10)           ->setNull();
        $config->addColumn(self::COLUMN_VAT)                  ->setString(14)           ->setNull();
        $config->addColumn(self::COLUMN_BILLING_ROW_1)        ->setString(63)           ->setNull();
        $config->addColumn(self::COLUMN_BILLING_ROW_2)        ->setString(63)           ->setNull();
        $config->addColumn(self::COLUMN_BILLING_ROW_3)        ->setString(127)          ->setNull();
        $config->addColumn(self::COLUMN_BILLING_ROW_4)        ->setString(127)          ->setNull();
        $config->addColumn(self::COLUMN_BILLING_ROW_5)        ->setString(10)           ->setNull();
        $config->addColumn(self::COLUMN_BILLING_ROW_6)        ->setFK(self::TABLE_COUNTRY)->setNull();
        $config->addColumn(self::COLUMN_DELIVERY_ROW_1)       ->setString(63)          ->setNull();
        $config->addColumn(self::COLUMN_DELIVERY_ROW_2)       ->setString(63)          ->setNull();
        $config->addColumn(self::COLUMN_DELIVERY_ROW_3)       ->setString(127)         ->setNull();
        $config->addColumn(self::COLUMN_DELIVERY_ROW_4)       ->setString(127)         ->setNull();
        $config->addColumn(self::COLUMN_DELIVERY_ROW_5)       ->setString(10)          ->setNull();
        $config->addColumn(self::COLUMN_DELIVERY_ROW_6)       ->setFK(self::TABLE_COUNTRY)    ->setNull();
        $config->addColumn(self::COLUMN_GENDER)               ->setEnum($internalNames)       ->setNull();
        $config->addColumn(self::COLUMN_HASH)                 ->setString(128)         ->setNull();

        $config->addKeyIndex([self::COLUMN_HASH])->setPrefixLength(self::COLUMN_HASH, 12);
        $config->addKeyIndex([self::COLUMN_TIMESTAMP]);
        $config->addKeyIndex([self::COLUMN_E_MAIL]);

        $config->addRelationMapped(self::TABLE_ORDER_ITEM);

        return $config;
    }
}
