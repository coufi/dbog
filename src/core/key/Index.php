<?php
/**
 * dbog .../src/core/key/Index.php
 */

namespace Src\Core\Key;

use Src\Exceptions\SyncerException;

class Index extends \Src\Core\Key
{
    const INDEX_PREFIX = 'ix_';

    /** @var array */
    protected $lengths;


    /**
     * {@inheritdoc}
     */
    protected function setKeyName()
    {
        $this->keyName = substr(self::INDEX_PREFIX . $this->getTableName() . '_' . implode('_', $this->columns), 0, self::MAX_KEY_NAME_LENGTH);
    }

    /**
     * Set index column prefix length.
     * @param string $column Column name
     * @param integer $length Prefix length
     * @return Index
     */
    public function setPrefixLength($column, $length)
    {
        $this->lengths[$column] = $length;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefixLength($column)
    {
        return isset ($this->lengths[$column]) ? $this->lengths[$column] : null;
    }

    /**
     *  Validate index key.
     * @throws SyncerException
     */
    public function validate()
    {
        $columns = $this->table->getColumns();
        foreach ($this->getColumns() as $columnName)
        {
            if (!isset ($columns[$columnName]))
            {
                throw new SyncerException("Indexed column {$columnName} not found in table {$this->getTableName()}");
            }
        }
    }
}
