<?php
/**
 * dbog .../demo/table/Product.php
 */

namespace Demo\Table;


class Product extends \Src\Core\Table
{
    const COLUMN_NAME   = 'name';
    const COLUMN_URI    = 'uri';

    const TABLE_PRODUCT_CATEGORY                = 'product_category';
    const TABLE_PRODUCT_HAS_PRODUCT_CATEGORY    = 'product_has_product_category';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()->setIntUnsigned();
        $config->addColumn(self::COLUMN_NAME)   ->setString(63)  ->setNull(false);
        $config->addColumn(self::COLUMN_URI)    ->setString()           ->setNull(false);

        $config->addKeyUnique([self::COLUMN_NAME]);
        $config->addKeyUnique([self::COLUMN_URI]);

        $config->addRelationConnection(self::TABLE_PRODUCT_CATEGORY, self::TABLE_PRODUCT_HAS_PRODUCT_CATEGORY);

        return $config;
    }
}
