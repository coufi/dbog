<?php
/**
 * dbog .../demo/table/MenuItemContent.php
 */

namespace Demo\Table;


class MenuItemContent extends \Src\Core\Table
{

    const COLUMN_ID_MENU_ITEM   = 'id_menu_item';
    const COLUMN_ID_CONTENT     = 'id_content';
    const COLUMN_NAME           = 'name';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()                           ->setSmallIntUnsigned();
        $config->addColumn(self::COLUMN_ID_MENU_ITEM)              ->setFK();
        $config->addColumn(self::COLUMN_ID_CONTENT)                ->setFK();
        $config->addColumn(self::COLUMN_NAME)                      ->setString(55)               ->setNull();

        $config->addKeyUnique([self::COLUMN_ID_MENU_ITEM]);

        return $config;
    }
}
