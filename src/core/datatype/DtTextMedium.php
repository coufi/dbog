<?php
/**
 * dbog .../src/core/datatype/DtTextMedium.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtTextMedium extends Datatype
{
    const MEDIUMTEXT_SQL_DATATYPE = 'mediumtext';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::MEDIUMTEXT_SQL_DATATYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::MEDIUMTEXT_SQL_DATATYPE;
    }

    /**
     * Get mediumtext max length.
     * @return int
     */
    public function getLength()
    {
        return 16777215;
    }
}
