<?php
/**
 * dbog .../src/core/table/Config.php
 */

namespace Src\Core\Table;

use Src\Core\Column;
use Src\Core\Key\Index;
use Src\Core\Key\Primary;
use Src\Core\Key\Unique;
use Src\Core\Relation\Connection;
use Src\Core\Relation\Extension;
use Src\Core\Relation\Mapped;
use Src\Core\Relation\Mapping;
use Src\Collection;
use Src\Core\Trigger;

class Config
{

    const ID_PREFIX = 'id_';

    /**  @var string */
    protected $tableName;

    /**  @var Collection */
    protected $tableContainer;

    /**  @var string  */
    protected $renamedFrom;

    /** @var Column[] */
    protected $columns;

    /** @var Trigger[] */
    protected $triggers;

    /** @var Mapping[] */
    protected $relationMapping;

    /** @var Mapped[] */
    protected $relationMapped;

    /** @var Connection[] */
    protected $relationConnection;

    /** @var Extension[] */
    protected $relationExtension;

    /** @var Primary */
    protected $keyPrimary;

    /** @var Unique[] */
    protected $keyUnique;

    /** @var Index[] */
    protected $keyIndex;

    /**
     * @param string $tableName
     * @param Collection $tableContainer
     */
    public function __construct($tableName, $tableContainer)
    {
        $this->tableName = $tableName;
        $this->tableContainer = $tableContainer;
        $this->columns = [];
        $this->triggers = [];
        $this->relationMapping = [];
        $this->relationMapped = [];
        $this->relationConnection = [];
        $this->relationExtension = [];
        $this->keyIndex = [];
        $this->keyUnique = [];
    }

    /**
     * Set renamed from.
     * @param string $renamedFrom
     * @return Config
     */
    public function setRenamedFrom($renamedFrom)
    {
        $this->renamedFrom = $renamedFrom;
        return $this;
    }

    /**
     * Get table container.
     * @return Collection
     */
    public function getTableContainer()
    {
        return $this->tableContainer;
    }

    /**
     * Add column.
     * @param $columnName string
     * @return Column
     */
    public function addColumn($columnName)
    {
        return $this->columns[$columnName] = new Column($columnName, $this);
    }

    /**
     * Add primary key column.
     * @return Column
     */
    public function addPrimary()
    {
        $columnName = self::ID_PREFIX . $this->tableName;
        $column = $this->addColumn($columnName);
        $this->addKeyPrimary([$columnName])->setAutoincremental();
        return $column;
    }


    /**
     * Add relation mapping.
     * @param string $reference Reference table name
     * @param array $columns Column names
     * @param array $targets Target column names
     * @return Mapping
     */
    public function addRelationMapping($reference, $columns, $targets = [])
    {
        //Add key index automatically
        $this->addKeyIndex($columns);

        return $this->relationMapping[] = new Mapping($this, $reference, $columns, $targets);
    }

    /**
     * Add relation mapped.
     * @param string $reference Reference table name
     * @return Mapped
     */
    public function addRelationMapped($reference)
    {
        return $this->relationMapped[] = new Mapped($this, $reference);
    }

    /**
     * Add relation connection.
     * @param string $reference Reference table name
     * @param string $connecting Connecting table name
     * @return Connection
     */
    public function addRelationConnection($reference, $connecting)
    {
        return $this->relationConnection[] = new Connection($this, $reference, $connecting);
    }

    /**
     * Add relation extension.
     * @param string $reference Reference table name
     * @return Extension
     */
    public function addRelationExtension($reference)
    {
        return $this->relationExtension[$reference] = new Extension($this, $reference);
    }

    /**
     * Add key primary.
     * @param array $columns Column names
     * @return Primary
     */
    protected function addKeyPrimary($columns)
    {
        return $this->keyPrimary = new Primary($this, $columns);
    }

    /**
     * Add key unique.
     * @param array $columns Column names
     * @return Unique
     */
    public function addKeyUnique($columns)
    {
        return $this->keyUnique[] = new Unique($this, $columns);
    }

    /**
     * Add key index.
     * @param array $columns Column names
     * @return Index
     */
    public function addKeyIndex($columns)
    {
        return $this->keyIndex[] = new Index($this, $columns);
    }

    /**
     * Add trigger.
     * @param $time string
     * @param $action string
     * @param $body string
     * @return Trigger
     */
    public function addTrigger($time, $action, $body)
    {
        return $this->triggers[] = new Trigger($this->tableName, $time, $action, $body);
    }

    /**
     * Get name.
     * @return string
     */
    public function getName()
    {
        return $this->tableName;
    }

    /**
     * Get renamed from name.
     * @return string
     */
    public function getRenamedFrom()
    {
        return $this->renamedFrom;
    }

    /**
     * Get columns.
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get triggers.
     * @return Trigger[]
     */
    public function getTriggers()
    {
        return $this->triggers;
    }

    /**
     * Get relations mapping.
     * @return Mapping[]
     */
    public function getRelationsMapping()
    {
        return $this->relationMapping;
    }

    /**
     * Get relations mapped.
     * @return Mapped[]
     */
    public function getRelationsMapped()
    {
        return $this->relationMapped;
    }

    /**
     * Get relations connection.
     * @return Connection[]
     */
    public function getRelationsConnection()
    {
        return $this->relationConnection;
    }

    /**
     * Get relations extension.
     * @return Extension[]
     */
    public function getRelationsExtension()
    {
        return $this->relationExtension;
    }

    /**
     * Get key primary.
     * @return Primary
     */
    public function getKeyPrimary()
    {
        return $this->keyPrimary;
    }

    /**
     * Get keys unique.
     * @return Unique[]
     */
    public function getKeysUnique()
    {
        return $this->keyUnique;
    }

    /**
     * Get keys index
     * @return Index[]
     */
    public function getKeysIndex()
    {
        return $this->keyIndex;
    }

    /**
     * Whether column exists.
     * @param $name string Column name
     * @return bool
     */
    public function hasColumn($name)
    {
        return isset ($this->columns[$name]);
    }

    /**
     * Get column by name.
     * @param $name string Column name
     * @return Column
     */
    public function getColumn($name)
    {
        return $this->columns[$name] ?? false;
    }
}
