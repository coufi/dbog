<?php
/**
 * dbog .../demo/table/ProductCategory.php
 */

namespace Demo\Table;


class ProductCategory extends \Src\Core\Table
{
    const COLUMN_NAME       = 'name';
    const COLUMN_URI        = 'uri';
    const COLUMN_CONTENT    = 'content';

    const TABLE_PRODUCT                         = 'product';
    const TABLE_PRODUCT_HAS_PRODUCT_CATEGORY    = 'product_has_product_category';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()->setSmallIntUnsigned();
        $config->addColumn(self::COLUMN_NAME)   ->setString(63)  ->setNull(false);
        $config->addColumn(self::COLUMN_URI)    ->setString()           ->setNull(false);
        $config->addColumn(self::COLUMN_CONTENT)->setText()             ->setNull();

        $config->addKeyUnique([self::COLUMN_NAME]);
        $config->addKeyUnique([self::COLUMN_URI]);
        $config->addRelationConnection(self::TABLE_PRODUCT, self::TABLE_PRODUCT_HAS_PRODUCT_CATEGORY);
        return $config;
    }
}
