<?php
/**
 * dbog .../src/core/datatype/DtSmallint.php
 */

namespace Src\Core\Datatype;


class DtSmallint extends DtInt
{
    const SMALLINT_SQL_DATATYPE = 'smallint';

    public function getSqlDefinition()
    {
        return self::SMALLINT_SQL_DATATYPE . ($this->unsigned ? self::UNSIGNED_DEFINITION : self::SIGNED_DEFINITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::SMALLINT_SQL_DATATYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlPrecision()
    {
        return 5;
    }
}
