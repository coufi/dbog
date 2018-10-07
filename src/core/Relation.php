<?php
/**
 * dbog .../src/core/Relation.php
 */

namespace Src\Core;


abstract class Relation
{
    /** @var string */
    protected $tableName;

    /** @var string */
    protected $relationName;

    /** @var string */
    protected $reference;

    /**
     * @param string $tableName
     * @param string $reference
     */
    public function __construct($tableName, $reference)
    {
        $this->tableName = $tableName;
        $this->reference = $reference;

        $this->setRelationName();
    }

    /**
     * Set relation name
     */
    protected function setRelationName()
    {
        $this->relationName = $this->reference;
    }

    /**
     * Get reference
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->relationName;
    }
}
