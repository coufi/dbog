<?php
/**
 * dbog .../src/core/datatype/DtString.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtString extends Datatype
{
    const STRING_SQL_DEFINITION = 'varchar';

    /** @var int */
    protected $length = 127;

    /**
     * Set string max length.
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::STRING_SQL_DEFINITION . '(' . $this->length . ')';
    }

    /**
     * Get string max length.
     * @return int
     */
    public function getSqlMaxLength()
    {
        return $this->length;
    }
}
