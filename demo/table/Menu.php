<?php
/**
 * dbog .../demo/table/Menu.php
 */

namespace Demo\Table;


class Menu extends \Src\Core\Table
{
    const COLUMN_NAME = 'name';

    const TABLE_MENU_ITEM = 'menu_item';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()                 ->setSmallIntUnsigned();
        $config->addColumn(self::COLUMN_NAME)            ->setString(60);

        $config->addKeyUnique([self::COLUMN_NAME]);
        $config->addRelationMapped(self::TABLE_MENU_ITEM);

        return $config;
    }
}
