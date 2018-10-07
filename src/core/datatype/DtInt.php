<?php
/**
 * dbog .../src/core/datatype/DtInt.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtInt extends Datatype
{
    const INT_SQL_DATATYPE = 'int';
    const SIGNED_DEFINITION = ' signed';

    /** @var bool  */
    protected $unsigned = false;

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::INT_SQL_DATATYPE . ($this->unsigned ? self::UNSIGNED_DEFINITION : self::SIGNED_DEFINITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::INT_SQL_DATATYPE;
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
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function getSqlScale()
    {
        return 0;
    }
}
