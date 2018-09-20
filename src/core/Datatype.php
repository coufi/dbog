<?php
/**
 * dbog .../src/core/Datatype.php
 */

namespace Src\Core;


abstract class Datatype
{
    const UNSIGNED_DEFINITION = ' unsigned';

    /**
     * Get SQL datatype definition.
     * @return string
     */
    abstract public function getSqlDefinition();

    public function getSqlMaxLength()
    {
        return null;
    }

    /**
     * Get SQL datatype precision.
     * @return int|null
     */
    public function getSqlPrecision()
    {
        return null;
    }

    /**
     * Get SQL datatype scale.
     * @return int|null
     */
    public function getSqlScale()
    {
        return null;
    }
}
