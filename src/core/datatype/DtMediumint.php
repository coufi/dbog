<?php
/**
 * dbog .../src/core/datatype/DtMediumint.php
 */

namespace Src\Core\Datatype;

class DtMediumint extends DtInt
{
    const MEDIUMINT_SQL_DEFINITION = 'mediumint';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::MEDIUMINT_SQL_DEFINITION . ($this->unsigned ? self::UNSIGNED_DEFINITION : self::SIGNED_DEFINITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlPrecision()
    {
        return 7;
    }
}
