<?php
/**
 * dbog .../src/core/key/Primary.php
 */

namespace Src\Core\Key;

class Primary extends \Src\Core\Key
{
    const PRIMARY_PREFIX = 'pk_';

    /** @var bool */
    protected $autoincremental;

    /**
     * {@inheritdoc}
     */
    protected function setKeyName()
    {
        $this->keyName = self::PRIMARY_PREFIX . $this->getTableName();
    }

    /**
     * Set autoincremental.
     * @return Primary
     */
    public function setAutoincremental()
    {
        $this->autoincremental = true;
        return $this;
    }

    /**
     * Whether is autoincremental.
     * @return bool
     */
    public function isAutoincremental()
    {
        return $this->autoincremental;
    }
}
