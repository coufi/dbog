<?php
/**
 * dbog .../src/core/relation/Extension.php
 */

namespace Src\Core\Relation;


use Src\Exceptions\SyncerException;

class Extension extends \Src\Core\Relation
{
    /**
     * Validate existing reference table.
     * @throws SyncerException
     */
    public function validate()
    {
        $tableContainer = $this->getTable()->getTableContainer();

        // validate existing reference to target table
        if (!$tableContainer->has($this->getReference()))
        {
            throw new SyncerException("Extension table from {$this->tableName} to {$this->reference} not found");
        }
    }
}
