<?php
/**
 * dbog .../src/core/key/Unique.php
 */

namespace Src\Core\Key;

class Unique extends \Src\Core\Key
{

    const UNIQUE_PREFIX = 'uq_';

    /**
     * {@inheritdoc}
     */
    protected function setKeyName()
    {
        $columns = implode('_', $this->columns);
        $this->keyName = substr(self::UNIQUE_PREFIX . $this->tableName . '_' . $columns, 0, self::MAX_KEY_NAME_LENGTH);
    }
}
