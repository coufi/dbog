<?php
/**
 * dbog .../src/core/relation/Mapping.php
 */

namespace Src\Core\Relation;


use Src\Core\Key;
use Src\Database\AdapterInterface;
use Src\Exceptions\SyncerException;

/**
 * Class Mapping
 * @package Src\Core\Relation
 * Represents one-to-many relation and foreign key in database.
 */
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
        $schema = $this->getTable()->getSchema();

        // validate existing reference to target table
        if (!$schema->hasTable($this->getReference()))
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
        $targetTableColumns = $schema->getTable($this->getReference())->getConfiguration()->getColumns();
        foreach ($this->getTargets() as $target)
        {
            if (!isset ($targetTableColumns[$target]))
            {
                throw new SyncerException("Mapping target {$target} not found in mapping from table {$this->tableName} to table {$this->getReference()}");
            }
        }
    }

    /**
     * Get information schema from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array ['table_name' => (string), 'ref_table_name' => (string), 'delete_rule' => (string), 'update_rule' => (string)]
     */
    public function getInformationSchema($db, $dbSchemaName)
    {
        $query = "
SELECT
  `R`.`TABLE_NAME` AS table_name,
  `R`.`REFERENCED_TABLE_NAME` AS ref_table_name,
  `R`.`DELETE_RULE` AS delete_rule,
  `R`.`UPDATE_RULE` AS update_rule
FROM `INFORMATION_SCHEMA`.`REFERENTIAL_CONSTRAINTS` AS `R`
WHERE `R`.`CONSTRAINT_SCHEMA` = '{$dbSchemaName}' AND `R`.`CONSTRAINT_NAME` = '{$this->getName()}'";
        return $db->fetch($query);
    }


    /**
     * {@inheritdoc}
     */
    public function sync($runner)
    {
        $informationSchema = $this->getInformationSchema($runner->getDb(), $runner->getDbSchemaName());

        // found definition in information schema, check for changes
        if ($informationSchema)
        {
            list ($tableName, $reference, $delete, $update) = $informationSchema;

            $recreate = false;

            // changed table name
            if ($tableName != $this->tableName)
            {
                $recreate = true;
            }

            // changed reference table name
            if ($reference != $this->getReference())
            {
                $recreate = true;
            }

            //changed on delete action
            if ($delete != $this->getOnDeleteAction())
            {
                $recreate = true;
            }

            //changed on update action
            if ($update != $this->getOnUpdateAction())
            {
                $recreate = true;
            }

            // definition has been changed, sync in db
            if ($recreate)
            {
                $runner->log("SYNC: Recreating mapping {$this->getName()}.");
                $runner->processQuery("ALTER TABLE {$tableName} DROP FOREIGN KEY {$this->getName()}");
                $runner->processQuery("ALTER TABLE `{$this->tableName}` ADD ") . $this->getSQLCreate();
            }
        }
        // definition not found, create new mapping relation
        else
        {
            $runner->log("SYNC: Creating mapping {$this->getName()}.");
            $runner->processQuery("ALTER TABLE `{$this->tableName}` ADD " . $this->getSQLCreate());
        }
    }

    /**
     * Get SQL create statement - mapping relation.
     * @return string
     */
    protected function getSQLCreate()
    {
        $target = count($this->getTargets()) ? $this->getTargets() : $this->getColumns();
        return
            "CONSTRAINT `{$this->getName()}` FOREIGN KEY (`" .
            implode('`, `', $this->getColumns()) .
            "`) REFERENCES `{$this->getReference()}` (`" .
            implode('`, `', $target) . '`)' .
            " ON DELETE " . $this->getOnDeleteAction() .
            " ON UPDATE " . $this->getOnUpdateAction();
    }
}
