<?php
/**
 * dbog .../src/core/datatype/DtYear.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtYear extends Datatype
{
    const YEAR_SQL_DATATYPE = 'year';

    /**
     * Get text max length.
     * @return int
     */
    public function getSqlDefinition()
    {
        return self::YEAR_SQL_DATATYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::YEAR_SQL_DATATYPE;
    }
}
