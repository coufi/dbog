<?php
/**
 * dbog .../src/core/datatype/DtTinyint.php
 */

namespace Src\Core\Datatype;


class DtTinyint extends DtInt
{
    const TINYINT_SQL_DEFINITION = 'tinyint';

    /**
     * Get text max length.
     * @return int
     */
    public function getSqlDefinition()
    {
        return self::TINYINT_SQL_DEFINITION . ($this->unsigned ? self::UNSIGNED_DEFINITION : self::SIGNED_DEFINITION);
    }

    /**
     * Get text max length.
     * @return int
     */
    public function getSqlPrecision()
    {
        return 3;
    }
}
