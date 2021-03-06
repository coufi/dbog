<?php
/**
 * dbog .../src/core/datatype/DtEnum.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtEnum extends Datatype
{
    const ENUM_SQL_DATATYPE = 'enum';

    /** @var array */
    protected $allowedValues;

    /**
     * @param array $allowedValues
     */
    public function __construct($allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDefinition()
    {
        return self::ENUM_SQL_DATATYPE . "('" . implode("','", $this->allowedValues) . "')";
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDatatype()
    {
        return self::ENUM_SQL_DATATYPE;
    }
}
