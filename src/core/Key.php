<?php
/**
 * dbog .../src/core/Key.php
 */

namespace Src\Core;


use Src\Core\Table\Config;

abstract class Key implements ValidableInterface
{
    const MAX_KEY_NAME_LENGTH = 63;

    /**  @var Config */
    protected $table;

    /** @var string */
    protected $keyName;

    /** @var array */
    protected $columns;

    /**
     * @param Config $table
     * @param array $columns
     */
    public function __construct($table, $columns)
    {
        $this->table = $table;
        $this->columns = $columns;

        $this->setKeyName();
    }

    /**
     *  Validate key.
     */
    public function validate()
    {
        // do nothing
    }

    /**
     * Set key name. Generated automatically.
     */
    protected function setKeyName()
    {
        $this->keyName = '';
    }

    /**
     * Set custom key name.
     * @param string $keyName
     */
    public function setCustomKeyName($keyName)
    {
        $this->keyName = $keyName;
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
     * Get column instances.
     * @return Column[]
     */
    public function getColumnInstances()
    {
        $columns = [];
        foreach ($this->columns as $column)
        {
            $columns[] = $this->table->getColumn($column);
        }


        return $columns;
    }

    /**
     * Get key name.
     * @return string
     */
    public function getName()
    {
        return $this->keyName;
    }

    /**
     * Get table name.
     * @return string
     */
    public function getTableName()
    {
        return $this->table->getName();
    }

    /**
     * Get table config.
     * @return Config
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get index prefix length
     * @param string $column Column name
     * @return integer|null
     */
    public function getPrefixLength($column)
    {
        return null;
    }
}
