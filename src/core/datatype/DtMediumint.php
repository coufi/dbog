<?php
/**
 * dbog .../src/core/datatype/DtMediumint.php
 */

namespace Src\Core\Datatype;

class DtMediumint extends DtInt
{
    const MEDIUMINT_SQL_DATATYPE = 'mediumint';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::MEDIUMINT_SQL_DATATYPE . ($this->unsigned ? self::UNSIGNED_DEFINITION : self::SIGNED_DEFINITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::MEDIUMINT_SQL_DATATYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlPrecision()
    {
        return 7;
    }
}
