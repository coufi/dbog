<?php
/**
 * dbog .../src/Collection.php
 */

namespace Src;


class Collection
{
    /** @var \Closure[] */
    protected $callbacks = [];

    /** @var Table[] */
    protected $instatiated = [];

    /**
     * Whether has instance.
     * @param string $itemName
     * @return bool
     */
    public function has($itemName)
    {
        return isset ($this->instatiated[$itemName]) || isset ($this->callbacks[$itemName]);
    }

    /**
     * Add new instance callback.
     * @param string $className
     */
    public function add($className)
    {
        /** @var $className Table */
        $itemName = $className::getTableLabel();

        $this->callbacks[$itemName] = function() use ($className) { return new $className($this); };
    }

    /**
     * Get instance.
     * @param string $itemName
     * @return Table|null
     */
    public function get($itemName)
    {
        if ($this->has($itemName) && !isset ($this->instatiated[$itemName]))
        {
            $this->instatiated[$itemName] = $this->callbacks[$itemName]();
        }

        return $this->instatiated[$itemName];
    }

    /**
     * Get all registered item keys
     * @return array
     */
    public function getItemKeys()
    {
        return array_keys($this->callbacks);
    }

    /**
     * Get all instances.
     * @return Table[]
     */
    public function getAll()
    {
        $return = [];
        foreach ($this->getItemKeys() as $itemName)
        {
            $return[$itemName] = $this->get($itemName);
        }

        return $return;
    }
}
