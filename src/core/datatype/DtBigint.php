<?php
/**
 * dbog .../src/core/datatype/DtBigint.php
 */

namespace Src\Core\Datatype;


class DtBigint extends DtInt
{
    const BIGINT_SQL_DATATYPE = 'bigint';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::BIGINT_SQL_DATATYPE . ($this->unsigned ? self::UNSIGNED_DEFINITION : self::SIGNED_DEFINITION);
    }


    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::BIGINT_SQL_DATATYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlPrecision()
    {
        return 20;
    }
}
