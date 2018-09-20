<?php
/**
 * dbog .../src/core/datatype/DtText.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtText extends Datatype
{
    const TEXT_SQL_DEFINITION = 'TEXT';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::TEXT_SQL_DEFINITION;
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
