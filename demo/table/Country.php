<?php
/**
 * dbog .../demo/table/Country.php
 */

namespace Demo\Table;


class Country extends \Src\Core\Table
{
    const COLUMN_CODE_2     = 'code_2';
    const COLUMN_NUMERIC_3  = 'numeric_3';
    const COLUMN_NAME       = 'name';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()                 ->setSmallIntUnsigned();
        $config->addColumn(self::COLUMN_CODE_2)          ->setChar(2);
        $config->addColumn(self::COLUMN_NUMERIC_3)       ->setChar(3);
        $config->addColumn(self::COLUMN_NAME)            ->setString(31);

        $config->addKeyUnique([self::COLUMN_CODE_2]);
        $config->addKeyUnique([self::COLUMN_NAME]);

        return $config;
    }
}
