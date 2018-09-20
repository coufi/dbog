<?php
/**
 * dbog .../src/core/datatype/DtDatetime.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtDatetime extends Datatype
{
    const DATETIME_SQL_DEFINITION = 'datetime';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::DATETIME_SQL_DEFINITION;
    }
}
