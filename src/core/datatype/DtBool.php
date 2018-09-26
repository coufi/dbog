<?php
/**
 * dbog .../src/core/datatype/DtBool.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtBool extends Datatype
{
    const BOOL_SQL_DEFINITION = 'tinyint(1) unsigned';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::BOOL_SQL_DEFINITION;
    }

    /**
     * Whether is unsigned.
     * @return bool
     */
    public function isUnsigned()
    {
        return $this->true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlPrecision()
    {
        return 3;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlScale()
    {
        return 0;
    }
}
