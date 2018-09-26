<?php
/**
 * dbog .../src/core/Column.php
 */

namespace Src\Core;

use Src\Core\Table\Config;

class Column
{
    /** @var string */
    protected $columnName;

    /**  @var Config */
    protected $table;

    /**
     * Whether can be null.
     * @var bool
     */
    protected $null;

    /**
     * Default value.
     * @var string
     */
    protected $default;

    /**
     * Original table name before renaming.
     * @var string
     */
    protected $renamedFrom;

    /**
     * @var Datatype
     */
    protected $datatype;


    /**
     * @param $columnName string
     * @param $table Config
     */
    public function __construct($columnName, $table)
    {
        $this->columnName = $columnName;
        $this->table = $table;
        $this->null = false;
        $this->default = null;
        $this->renamedFrom = null;
        $this->datatype = null;
    }

    /**
     * Set whether column can be null.
     * @param boolean $null
     * @return Column
     */
    public function setNull($null = true)
    {
        $this->null = $null;
        return $this;
    }

    /**
     * Set column default value.
     * @param $default string
     * @return Column
     */
    public function setDefault($default)
    {
        if (is_bool($default))
        {
            $default = $default ? 1 : 0;
        }

        $this->default = $default;
        return $this;
    }

    /**
     * Set renamed from.
     * @param $renamedFrom string
     * @return Column
     */
    public function setRenamedFrom($renamedFrom)
    {
        $this->renamedFrom = $renamedFrom;
        return $this;
    }

    /**
     * Set foreign key.
     * @param null $tableName Referenced table name
     * @return Column
     */
    public function setFK($tableName = null)
    {
        // autogenerate if null
        if (is_null($tableName))
        {
            $tableName = substr($this->columnName, 3);      //remove 'id_'
        }

        $this->table->addRelationMapping($tableName, [$this->columnName], [Config::ID_PREFIX . $tableName]);

        return $this;
    }

    /**
     * Set datatype tiny int signed.
     * @return Column
     */
    public function setTinyIntSigned()
    {
        $this->datatype = new Datatype\DtTinyint();
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype tiny int unsigned.
     * @return Column
     */
    public function setTinyIntUnsigned()
    {
        $this->datatype = new Datatype\DtTinyint();
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype small int signed.
     * @return Column
     */
    public function setSmallIntSigned()
    {
        $this->datatype = new Datatype\DtSmallint();
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype small int unsigned.
     * @return Column
     */
    public function setSmallIntUnsigned()
    {
        $this->datatype = new Datatype\DtSmallint();
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype medium int signed.
     * @return Column
     */
    public function setMediumIntSigned()
    {
        $this->datatype = new Datatype\DtMediumint();
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype medium int unsigned.
     * @return Column
     */
    public function setMediumIntUnsigned()
    {
        $this->datatype = new Datatype\DtMediumint();
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype int signed.
     * @return Column
     */
    public function setIntSigned()
    {
        $this->datatype = new Datatype\DtInt();
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype int unsigned.
     * @return Column
     */
    public function setIntUnsigned()
    {
        $this->datatype = new Datatype\DtInt();
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype big int signed.
     * @return Column
     */
    public function setBigIntSigned()
    {
        $this->datatype = new Datatype\DtBigint();
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype big int unsigned.
     * @return Column
     */
    public function setBigIntUnsigned()
    {
        $this->datatype = new Datatype\DtBigint();
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype decimal signed.
     * @param int $precision Total decimal precision
     * @param int $fraction Num of fraction digits in decimal
     * @return Column
     */
    public function setDecimalSigned($precision = 10, $fraction = 0)
    {
        $this->datatype = new Datatype\DtDecimal();
        $this->datatype->setPrecision($precision);
        $this->datatype->setFraction($fraction);
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype decimal unsigned.
     * @param int $precision Total decimal precision
     * @param int $fraction Num of fraction digits in decimal
     * @return Column
     */
    public function setDecimalUnsigned($precision = 10, $fraction = 0)
    {
        $this->datatype = new Datatype\DtDecimal();
        $this->datatype->setPrecision($precision);
        $this->datatype->setFraction($fraction);
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype string.
     * @param int $length Total string length
     * @return Column
     */
    public function setString($length = null)
    {
        $this->datatype = new Datatype\DtString();
        $this->datatype->setLength($length);
        return $this;
    }

    /**
     * Set datatype char.
     * @param int $length Total string length
     * @return Column
     */
    public function setChar($length = null)
    {
        $this->datatype = new Datatype\DtChar();
        $this->datatype->setLength($length);
        return $this;
    }

    /**
     * Set datatype bool.
     * @return Column
     */
    public function setBool()
    {
        $this->datatype = new Datatype\DtBool();
        return $this;
    }

    /**
     * Set datatype year.
     * @return Column
     */
    public function setYear()
    {
        $this->datatype = new Datatype\DtYear();
        return $this;
    }

    /**
     * Set datatype date.
     * @return Column
     */
    public function setDate()
    {
        $this->datatype = new \Src\Core\Datatype\DtDate();
        return $this;
    }

    /**
     * Set datatype datetime.
     * @return Column
     */
    public function setDatetime()
    {
        $this->datatype = new \Src\Core\Datatype\DtDatetime();
        return $this;
    }

    /**
     * Set datatype time.
     * @return Column
     */
    public function setTime()
    {
        $this->datatype = new \Src\Core\Datatype\DtTime();
        return $this;
    }

    /**
     * Set datatype text.
     * @return Column
     */
    public function setText()
    {
        $this->datatype = new \Src\Core\Datatype\DtText();
        return $this;
    }

    /**
     * Set datatype text medium.
     * @return Column
     */
    public function setTextMedium()
    {
        $this->datatype = new \Src\Core\Datatype\DtTextMedium();
        return $this;
    }

    /**
     * Set datatype enum.
     * @param $allowedValues array
     * @return Column
     */
    public function setEnum($allowedValues = [])
    {
        $this->datatype = new Datatype\DtEnum($allowedValues);
        return $this;
    }

    /**
     * Set datatype set.
     * @param array $allowedValues
     * @return Column
     */
    public function setSet($allowedValues = [])
    {
        $this->datatype = new Datatype\DtSet($allowedValues);
        return $this;
    }

    /**
     * Get column name.
     * @return string
     */
    public function getName()
    {
        return $this->columnName;
    }

    /**
     * Whether is null.
     * @return boolean
     */
    public function isNull()
    {
        return $this->null;
    }

    /**
     * Get default value.
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get datatype.
     * @return Datatype
     */
    public function getDatatype()
    {
        return $this->datatype;
    }

    /**
     * Get renamed from name.
     * @return string
     */
    public function getRenamedFrom()
    {
        return $this->renamedFrom;
    }
}