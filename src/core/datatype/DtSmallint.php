<?php
/**
 * dbog .../src/core/datatype/DtSmallint.php
 */

namespace Src\Core\Datatype;


class DtSmallint extends DtInt
{
    const SMALLINT_SQL_DEFINITION = 'smallint';

    public function getSqlDefinition()
    {
        return self::SMALLINT_SQL_DEFINITION . ($this->unsigned ? self::UNSIGNED_DEFINITION : self::SIGNED_DEFINITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlPrecision()
    {
        return 5;
    }
}
