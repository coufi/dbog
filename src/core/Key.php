<?php
/**
 * dbog .../src/core/Key.php
 */

namespace Src\Core;


abstract class Key
{
    const MAX_KEY_NAME_LENGTH = 63;

    /**  @var string */
    protected $tableName;

    /** @var string */
    protected $keyName;

    /** @var array */
    protected $columns;

    /**
     * @param $tableName string
     * @param $columns array
     */
    public function __construct($tableName, $columns)
    {
        $this->tableName = $tableName;
        $this->columns = $columns;

        $this->setKeyName();
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
        return $this->tableName;
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
