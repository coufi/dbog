<?php
/**
 * dbog .../demo/table/MenuItem.php
 */

namespace Demo\Table;


class MenuItem extends \Src\Core\Table
{
    const PRODUCT_CATEGORY = "PRODUCT_CATEGORY";
    const CONTENT          = "CONTENT";
    const URL              = "URL";

    const COLUMN_MENU_ITEM_TYPE = 'menu_item';
    const COLUMN_ID_MENU        = 'id_menu';
    const COLUMN_NAME           = 'name';

    const TABLE_MENU_ITEM_PRODUCT_CATEGORY  = 'menu_item_product_category';
    const TABLE_MENU_ITEM_CONTENT           = 'menu_item_content';
    const TABLE_MENU_ITEM_URL               = 'menu_item_url';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $internalNames = [
            self::PRODUCT_CATEGORY,
            self::CONTENT,
            self::URL,
        ];

        $config = $this->createConfig();
        $config->addPrimary()                     ->setIntUnsigned();
        $config->addColumn(self::COLUMN_MENU_ITEM_TYPE)      ->setEnum($internalNames);
        $config->addColumn(self::COLUMN_ID_MENU)             ->setFK();
        $config->addColumn(self::COLUMN_NAME)                ->setString(55);

        $config->addKeyUnique([self::COLUMN_NAME]);

        $config->addRelationExtension(self::TABLE_MENU_ITEM_PRODUCT_CATEGORY);
        $config->addRelationExtension(self::TABLE_MENU_ITEM_CONTENT);
        $config->addRelationExtension(self::TABLE_MENU_ITEM_URL);

        return $config;
    }
}
