<?php
/**
 * dbog .../demo/table/ProductHasProductCategory.php
 */

namespace Demo\Table;


class ProductHasProductCategory extends \Src\Core\Table
{
    const COLUMN_ID_PRODUCT             = 'id_product';
    const COLUMN_ID_PRODUCT_CATEGORY    = 'id_product_category';
    const COLUMN_PRIORITY               = 'priority';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()                                              ->setIntUnsigned();
        $config->addColumn(self::COLUMN_ID_PRODUCT)             ->setFK();
        $config->addColumn(self::COLUMN_ID_PRODUCT_CATEGORY)    ->setFK();
        $config->addColumn(self::COLUMN_PRIORITY)               ->setIntUnsigned()    ->setNull();

        $config->addKeyUnique([self::COLUMN_ID_PRODUCT, self::COLUMN_ID_PRODUCT_CATEGORY]);

        return $config;
    }
}
