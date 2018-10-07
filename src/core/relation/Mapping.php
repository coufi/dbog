<?php
/**
 * dbog .../src/core/relation/Mapping.php
 */

namespace Src\Core\Relation;


use Src\Core\Key;

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
    public function __construct($tableName, $reference, $columns, $targets = [])
    {
        $this->columns = $columns;
        $this->targets = $targets;
        $this->onUpdateAction = self::NO_ACTION;
        $this->onDeleteAction = self::NO_ACTION;

        parent::__construct($tableName, $reference);
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
}