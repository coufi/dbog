<?php
/**
 * dbog .../src/core/key/Unique.php
 */

namespace Src\Core\Key;

use Src\Exceptions\SyncerException;

class Unique extends \Src\Core\Key
{

    const UNIQUE_PREFIX = 'uq_';

    /**
     * {@inheritdoc}
     */
    protected function setKeyName()
    {
        $columns = implode('_', $this->columns);
        $this->keyName = substr(self::UNIQUE_PREFIX . $this->getTableName() . '_' . $columns, 0, self::MAX_KEY_NAME_LENGTH);
    }

    /**
     *  Validate unique key.
     * @throws SyncerException
     */
    public function validate()
    {
        $columns = $this->table->getColumns();
        foreach ($this->getColumns() as $columnName)
        {
            if (!isset ($columns[$columnName]))
            {
                throw new SyncerException("Unique column {$columnName} not found in table {$this->getTableName()}");
            }
        }
    }
}
