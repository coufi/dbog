<?php
/**
 * dbog .../src/core/TableContainer.php
 */

namespace Src\Core;


abstract class TableContainer
{

    /** @var \Closure[] */
    protected $callbacks = [];

    /** @var Table[] */
    protected $instatiated = [];

    /**
     * Register all required classes.
     */
    abstract public function init();

    /**
     * Whether has instance.
     * @param string $className
     * @return bool
     */
    public function has($className)
    {
        return isset ($this->instatiated[$className]) || isset ($this->callbacks[$className]);
    }

    /**
     * Add new instance callback.
     * @param string $className
     */
    public function add($className)
    {
        $this->callbacks[$className] = function() use ($className) { return new $className(); };
    }

    /**
     * Get instance.
     * @param string $className
     * @return Table|null
     */
    public function get($className)
    {
        if ($this->has($className) && !isset ($this->instatiated[$className]))
        {
            $this->instatiated[$className] = $this->callbacks[$className]();
        }

        return $this->instatiated[$className];
    }

    /**
     * Get all registered class names
     * @return array
     */
    public function getClassNames()
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
        foreach ($this->getClassNames() as $className)
        {
            $return[$className] = $this->get($className);
        }

        return $return;
    }
}
