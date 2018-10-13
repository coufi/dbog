<?php
/**
 * dbog .../demo/table/MenuItemProductCategory.php
 */

namespace Demo\Table;


class MenuItemProductCategory extends \Src\Core\Table
{

    const COLUMN_ID_MENU_ITEM           = 'id_menu_item';
    const COLUMN_ID_PRODUCT_CATEGORY    = 'id_product_category';
    const COLUMN_NAME                   = 'name';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()                           ->setSmallIntUnsigned();
        $config->addColumn(self::COLUMN_ID_MENU_ITEM)              ->setFK();
        $config->addColumn(self::COLUMN_ID_PRODUCT_CATEGORY)       ->setFK();
        $config->addColumn(self::COLUMN_NAME)                      ->setString(55)               ->setNull();

        $config->addKeyUnique([self::COLUMN_ID_MENU_ITEM]);

        return $config;
    }
}
