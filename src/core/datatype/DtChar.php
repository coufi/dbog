<?php
/**
 * dbog .../src/core/datatype/DtChar.php
 */

namespace Src\Core\Datatype;

class DtChar extends DtString
{
    const CHAR_SQL_DATATYPE = 'char';

    /** @var int */
    protected $length = 2;

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::CHAR_SQL_DATATYPE . '(' . $this->length . ')';
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::CHAR_SQL_DATATYPE;
    }
}
