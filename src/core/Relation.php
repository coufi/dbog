<?php
/**
 * dbog .../src/core/Relation.php
 */

namespace Src\Core;


use Src\Core\Table\Config;
use Src\Syncer\Runner;

abstract class Relation implements ValidableInterface
{
    /** @var string */
    protected $tableName;

    /** @var Config */
    protected $table;

    /** @var string */
    protected $relationName;

    /** @var string */
    protected $reference;

    /**
     * @param Config $table
     * @param string $reference
     */
    public function __construct($table, $reference)
    {
        $this->tableName = $table->getName();
        $this->table = $table;
        $this->reference = $reference;

        $this->setRelationName();
    }

    /**
     *  Validate relation.
     */
    public function validate()
    {
        // do nothing
    }

    /**
     *  Sync relation with database.
     * @param Runner $runner
     */
    public function sync($runner)
    {
        // do nothing
    }

    /**
     * Set relation name.
     */
    protected function setRelationName()
    {
        $this->relationName = $this->reference;
    }

    /**
     * Get reference.
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Get name.
     * @return string
     */
    public function getName()
    {
        return $this->relationName;
    }

    /**
     * Get table config.
     * @return Config
     */
    public function getTable()
    {
        return $this->table;
    }
}
