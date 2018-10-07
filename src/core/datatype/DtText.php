<?php
/**
 * dbog .../src/core/datatype/DtText.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtText extends Datatype
{
    const TEXT_SQL_DATATYPE = 'text';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::TEXT_SQL_DATATYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::TEXT_SQL_DATATYPE;
    }

    /**
     * Get text max length.
     * @return int
     */
    public function getLength()
    {
        return 65535;
    }
}
