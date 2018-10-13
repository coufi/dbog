<?php
/**
 * dbog .../demo/table/Currency.php
 */

namespace Demo\Table;


class Currency extends \Src\Core\Table
{
    const COLUMN_CURRENCY_SHORT     = 'currency_short';
    const COLUMN_CURRENCY_SYMBOL    = 'currency_symbol';

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        $config->addPrimary()->setSmallIntUnsigned();
        $config->addColumn(self::COLUMN_CURRENCY_SHORT) ->setString(3);
        $config->addColumn(self::COLUMN_CURRENCY_SYMBOL)->setString(5);

        $config->addKeyUnique([self::COLUMN_CURRENCY_SHORT]);
        $config->addKeyUnique([self::COLUMN_CURRENCY_SYMBOL]);

        return $config;
    }
}
