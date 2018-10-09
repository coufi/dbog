<?php
/**
 * dbog .../src/core/relation/Mapping.php
 */

namespace Src\Core\Relation;


use Src\Core\Key;
use Src\Exceptions\SyncerException;

class Mapping extends \Src\Core\Relation
{
    const MAPPING_PREFIX = 'fk_';
    const RESTRICT = 'RESTRICT';
    const CASCADE = 'CASCADE';
    const NO_ACTION = 'NO ACTION';

    /** @var array */
    protected $columns;

    /**  @var array */
    protected $targets;

    /** @var string */
    protected $onUpdateAction;

    /** @var string */
    protected $onDeleteAction;

    /**
     * {@inheritdoc}
     * @param array $columns
     * @param array $targets
     */
    public function __construct($table, $reference, $columns, $targets = [])
    {
        $this->columns = $columns;
        $this->targets = $targets;
        $this->onUpdateAction = self::NO_ACTION;
        $this->onDeleteAction = self::NO_ACTION;

        parent::__construct($table, $reference);
    }


    /**
     * {@inheritdoc}
     */
    protected function setRelationName()
    {
        $columns = implode('_', $this->columns);
        $this->relationName = substr(self::MAPPING_PREFIX . $this->tableName . '_' . $columns, 0, Key::MAX_KEY_NAME_LENGTH);
    }

    /**
     * Set on update action.
     * @param string $action
     * @return Mapping
     */
    public function setOnUpdateAction($action)
    {
        $this->onUpdateAction = $action;
        return $this;
    }

    /**
     * Set on delete action.
     * @param string $action
     * @return Mapping
     */
    public function setOnDeleteAction($action)
    {
        $this->onDeleteAction = $action;
        return $this;
    }

    /**
     * Get columns.
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get target columns.
     * @return array
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Get on update action.
     * @return string
     */
    public function getOnUpdateAction()
    {
        return $this->onUpdateAction;
    }

    /**
     * Get on delete action.
     * @return string
     */
    public function getOnDeleteAction()
    {
        return $this->onDeleteAction;
    }

    /**
     * Validate existing reference table, existing columns, existing target columns
     * @throws SyncerException
     */
    public function validate()
    {
        $tableContainer = $this->getTable()->getTableContainer();

        // validate existing reference to target table
        if (!$tableContainer->has($this->getReference()))
        {
            throw new SyncerException("Mapping reference from table {$this->tableName} to {$this->reference} not found");
        }

        // validate existing columns
        $columns = $this->getTable()->getColumns();
        foreach ($this->getColumns() as $columnName)
        {
            if (!isset ($columns[$columnName]))
            {
                throw new SyncerException("Mapping column $columnName not found in table {$this->tableName}");
            }
        }

        // validate existing target columns
        $targetTableColumns = $tableContainer->get($this->getReference())->getConfiguration()->getColumns();
        foreach ($this->getTargets() as $target)
        {
            if (!isset ($targetTableColumns[$target]))
            {
                throw new SyncerException("Mapping target {$target} not found in mapping from table {$this->tableName} to table {$this->getReference()}");
            }
        }
    }
}