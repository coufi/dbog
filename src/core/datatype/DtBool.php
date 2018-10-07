<?php
/**
 * dbog .../src/core/datatype/DtBool.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtBool extends Datatype
{
    const BOOL_SQL_DEFINITION = 'tinyint(1) unsigned';
    const BOOL_SQL_DATATYPE = 'tinyint';

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::BOOL_SQL_DEFINITION;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::BOOL_SQL_DATATYPE;
    }

    /**
     * Whether is unsigned.
     * @return bool
     */
    public function isUnsigned()
    {
        return true;
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
