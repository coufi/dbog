<?php
/**
 * dbog .../src/core/relation/Extension.php
 */

namespace Src\Core\Relation;


use Src\Exceptions\SyncerException;

/**
 * Class Extension
 * @package Src\Core\Relation
 * Represents one-to-one relation.
 */
class Extension extends \Src\Core\Relation
{
    /**
     * Validate existing reference table.
     * @throws SyncerException
     */
    public function validate()
    {
        $schema = $this->getTable()->getSchema();

        // validate existing reference to target table
        if (!$schema->hasTable($this->getReference()))
        {
            throw new SyncerException("Extension table from {$this->tableName} to {$this->reference} not found");
        }
    }
}
