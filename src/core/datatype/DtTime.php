<?php
/**
 * dbog .../src/core/datatype/DtTime.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtTime extends Datatype
{
    const TIME_SQL_DEFINITION = 'time';

    /**
     * Get text max length.
     * @return int
     */
    public function getSqlDefinition()
    {
        return self::TIME_SQL_DEFINITION;
    }
}
