<?php
/**
 * dbog .../demo/table/Product.php
 */

namespace Demo\Table;


class Product extends \Src\Core\Table
{
    const COLUMN_NAME = 'name';
    const COLUMN_URI = 'uri';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()->setBigIntUnsigned();
        $config->addColumn(self::COLUMN_NAME)   ->setString(63)  ->setNull(false);
        $config->addColumn(self::COLUMN_URI)    ->setString()  ->setNull(false);

        $config->addKeyUnique([self::COLUMN_URI]);
        return $config;
    }
}