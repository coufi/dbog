<?php
/**
 * dbog .../src/core/datatype/DtSet.php
 */

namespace Src\Core\Datatype;


use Src\Core\Datatype;

class DtSet extends Datatype
{
    const SET_SQL_DEFINITION = 'set';

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
        return self::SET_SQL_DEFINITION . "('" . implode("','", $this->allowedValues) . "')";
    }
}
