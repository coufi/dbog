<?php
/**
 * dbog .../src/core/relation/Connection.php
 */

namespace Src\Core\Relation;


use Src\Exceptions\SyncerException;

class Connection extends \Src\Core\Relation
{
    /**
     * Connecting table name
     * @var string
     */
    protected $connecting;

    /**
     * {@inheritdoc}
     * @param string $connecting Connecting table name
     */
    public function __construct($tableName, $reference, $connecting)
    {
        $this->connecting = $connecting;
        parent::__construct($tableName, $reference);
    }

    /**
     * {@inheritdoc}
     */
    protected function setRelationName()
    {
        $this->relationName = $this->connecting;
    }

    /**
     * Get connecting
     * @return string
     */
    public function getConnecting()
    {
        return $this->connecting;
    }

    /**
     * Validate existing reference and connecting tables.
     * @throws SyncerException
     */
    public function validate()
    {
        $tableContainer = $this->getTable()->getTableContainer();

        // validate existing reference to target table
        if (!$tableContainer->has($this->getReference()))
        {
            throw new SyncerException("Connection reference from table {$this->tableName} to {$this->reference} not found");
        }

        // validate existing connecting table
        if (!$tableContainer->has($this->getConnecting()))
        {
            throw new SyncerException("Connecting table {$this->getConnecting()} defined in {$this->tableName} table not found");
        }
    }
}
