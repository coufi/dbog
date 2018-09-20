<?php
/**
 * dbog .../src/core/datatype/DtInt.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtInt extends Datatype
{
    const INT_SQL_DEFINITION = 'int';
    const SIGNED_DEFINITION = ' signed';

    /** @var bool  */
    protected $unsigned = false;

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::INT_SQL_DEFINITION . ($this->unsigned ? self::UNSIGNED_DEFINITION : self::SIGNED_DEFINITION);
    }

    /**
     * Signed / unsigned tinyint
     * @param bool $unsigned
     */
    public function setUnsigned($unsigned)
    {
        $this->unsigned = $unsigned;
    }

    /**
     * Whether is unsigned.
     * @return bool
     */
    public function isUnsigned()
    {
        return $this->unsigned;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlPrecision()
    {
        return 10;
    }
}
