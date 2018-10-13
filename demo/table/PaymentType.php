<?php
/**
 * dbog .../demo/table/PaymentType.php
 */

namespace Demo\Table;


class PaymentType extends \Src\Core\Table
{
    const COLUMN_NAME           = 'name';
    const COLUMN_PAYMENT_PHASE  = 'payment_phase';
    const COLUMN_CASH           = 'cash';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()->setTinyIntUnsigned();
        $config->addColumn(self::COLUMN_PAYMENT_PHASE)  ->setTinyIntSigned()  ->setNull();
        $config->addColumn(self::COLUMN_CASH)           ->setBool()           ->setDefault(false);
        $config->addColumn(self::COLUMN_NAME)           ->setString(31);

        $config->addKeyUnique([self::COLUMN_NAME]);

        return $config;
    }
}
