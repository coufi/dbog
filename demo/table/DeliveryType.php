<?php
/**
 * dbog .../demo/table/DeliveryType.php
 */

namespace Demo\Table;


class DeliveryType extends \Src\Core\Table
{
    const COLUMN_NAME = 'name';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()->setBigIntUnsigned();
        $config->addPrimary()->setTinyIntUnsigned();
        $config->addColumn(self::COLUMN_NAME)           ->setString(31);

        $config->addKeyUnique([self::COLUMN_NAME]);

        return $config;
    }
}
