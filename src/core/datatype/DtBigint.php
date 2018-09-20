<?php
/**
 * dbog .../src/core/datatype/DtBigint.php
 */

namespace Src\Core\Datatype;


class DtBigint extends DtInt
{
    const BIGINT_SQL_DEFINITION = 'bigint';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::BIGINT_SQL_DEFINITION . ($this->unsigned ? self::UNSIGNED_DEFINITION : self::SIGNED_DEFINITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlPrecision()
    {
        return 20;
    }
}
