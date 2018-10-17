<?php
/**
 * dbog .../src/core/Key.php
 */

namespace Src\Core;


use Src\Core\Table\Config;
use Src\Exceptions\SyncerException;
use Src\Syncer\Runner;

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
        $this->columns = is_array($columns) ? $columns : [];

        $this->setKeyName();
    }

    /**
     *  Validate key.
     * @throws SyncerException
     */
    public function validate()
    {
        if (!$this->columns)
        {
            throw new SyncerException("Key {$this->getName()} for table {$this->getTableName()} does not have specified any column");
        }
    }

    /**
     *  Sync key with database.
     * @param Runner $runner
     */
    public function sync($runner)
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
     * Get SQL create statement.
     * @return string
     */
    public abstract function getSQLCreate();

    /**
     * Get string with list of columns for SQL statement.
     * @return string
     */
    protected function getColumnsListToSQL()
    {
        $columnsList = [];
        foreach ($this->getColumns() as $i => $column)
        {
            $prefixLength = '';
            // prefix lenght used for index key, otherwise is null
            if ($length = $this->getPrefixLength($column))
            {
                $prefixLength = "($length)";
            }

            $columnsList[] = "`$column`$prefixLength";
        }

        return implode(', ', $columnsList) ;
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
