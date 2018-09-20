<?php
/**
 * dbog .../src/core/key/Index.php
 */

namespace Src\Core\Key;

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
        $this->keyName = substr(self::INDEX_PREFIX . $this->tableName . '_' . implode('_', $this->columns), 0, self::MAX_KEY_NAME_LENGTH);
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
}
