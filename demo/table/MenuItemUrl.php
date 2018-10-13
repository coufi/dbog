<?php
/**
 * dbog .../demo/table/MenuItemUrl.php
 */

namespace Demo\Table;


class MenuItemUrl extends \Src\Core\Table
{

    const COLUMN_ID_MENU_ITEM   = 'id_menu_item';
    const COLUMN_URL            = 'url';
    const COLUMN_NAME           = 'name';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()                           ->setSmallIntUnsigned();
        $config->addColumn(self::COLUMN_ID_MENU_ITEM)              ->setFK();
        $config->addColumn(self::COLUMN_URL)                       ->setString(511);
        $config->addColumn(self::COLUMN_NAME)                      ->setString(55)               ->setNull();

        $config->addKeyUnique([self::COLUMN_ID_MENU_ITEM]);

        return $config;
    }
}