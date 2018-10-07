<?php
/**
 * dbog .../src/core/datatype/DtDate.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtDate extends Datatype
{
    const DATE_SQL_DATATYPE = 'date';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::DATE_SQL_DATATYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::DATE_SQL_DATATYPE;
    }
}
