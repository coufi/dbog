<?php
/**
 * dbog .../src/core/datatype/DtTime.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtTime extends Datatype
{
    const TIME_SQL_DATATYPE = 'time';

    /**
     * Get text max length.
     * @return int
     */
    public function getSqlDefinition()
    {
        return self::TIME_SQL_DATATYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::TIME_SQL_DATATYPE;
    }
}
