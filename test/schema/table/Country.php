<?php
/**
 * dbog .../test/schema/table/Country.php
 */

/**
 * Used for removed column test case.
 */

namespace Test\Schema\Table;


class Country extends \Src\Core\Table
{
    const COLUMN_CODE_2     = 'code_2';
    const COLUMN_NAME       = 'name';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()                 ->setSmallIntUnsigned();
        $config->addColumn(self::COLUMN_CODE_2)          ->setChar(2);
        // column numeric_3 not specified - should be detected as removed.
        $config->addColumn(self::COLUMN_NAME)            ->setString(31);

        $config->addKeyUnique([self::COLUMN_CODE_2]);
        $config->addKeyUnique([self::COLUMN_NAME]);

        return $config;
    }
}
