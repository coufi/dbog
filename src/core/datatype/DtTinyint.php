<?php
/**
 * dbog .../src/core/datatype/DtTinyint.php
 */

namespace Src\Core\Datatype;


class DtTinyint extends DtInt
{
    const TINYINT_SQL_DATATYPE = 'tinyint';

    /**
     * Get text max length.
     * @return int
     */
    public function getSqlDefinition()
    {
        return self::TINYINT_SQL_DATATYPE . ($this->unsigned ? self::UNSIGNED_DEFINITION : self::SIGNED_DEFINITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::TINYINT_SQL_DATATYPE;
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
