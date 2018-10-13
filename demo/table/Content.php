<?php
/**
 * dbog .../demo/table/Content.php
 */

namespace Demo\Table;


class Content extends \Src\Core\Table
{
    const COLUMN_NAME       = 'name';
    const COLUMN_URI        = 'uri';
    const COLUMN_CONTENT    = 'content';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()->setIntUnsigned();
        $config->addColumn(self::COLUMN_NAME)       ->setString(63)  ->setNull(false);
        $config->addColumn(self::COLUMN_URI)        ->setString()           ->setNull(false);
        $config->addColumn(self::COLUMN_CONTENT)    ->setText()             ->setNull();

        $config->addKeyUnique([self::COLUMN_NAME]);
        $config->addKeyUnique([self::COLUMN_URI]);

        return $config;
    }
}
