<?php
/**
 * dbog .../src/core/datatype/DtDecimal.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtDecimal extends Datatype
{
    const DECIMAL_SQL_DEFINITION = 'decimal';

    /** @var bool */
    protected $unsigned = false;

    /** @var int */
    protected $precision = 10;

    /** @var int */
    protected $fraction = 0;

    /**
     * Enable / disable unsigned decimal.
     * @param bool $unsigned
     */
    public function setUnsigned($unsigned)
    {
        $this->unsigned = $unsigned;
    }

    /**
     * Set total decimal precision.
     * @param int $precision
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;
    }

    /**
     * Set num of fraction digits in decimal.
     * @param int $fraction
     */
    public function setFraction($fraction)
    {
        $this->fraction = $fraction;
    }

    /**
     * Get total decimal precision.
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * Get num of fraction digits in decimal.
     * @return int
     */
    public function getFraction()
    {
        return $this->fraction;
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
    public function getSqlDefinition()
    {
        return self::DECIMAL_SQL_DEFINITION . '(' . $this->precision . ',' . $this->fraction . ')' . ($this->unsigned ? self::UNSIGNED_DEFINITION : '');
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlPrecision()
    {
        return $this->precision;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlScale()
    {
        return $this->fraction;
    }
}
