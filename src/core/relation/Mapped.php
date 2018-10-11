<?php
/**
 * dbog .../src/core/relation/Mapped.php
 */

namespace Src\Core\Relation;


use Src\Exceptions\SyncerException;

class Mapped extends \Src\Core\Relation
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
            throw new SyncerException("Mapped table from {$this->tableName} to {$this->reference} not found");
        }
    }
}
