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
     * @param string $tableName
     * @return bool
     */
    public function has($tableName)
    {
        return isset ($this->instatiated[$tableName]) || isset ($this->callbacks[$tableName]);
    }

    /**
     * Add new instance callback.
     * @param string $className
     */
    public function add($className)
    {
        /** @var $className Table */
        $tableName = $className::getTableLabel();

        $this->callbacks[$tableName] = function() use ($className) { return new $className($this); };
    }

    /**
     * Get instance.
     * @param string $tableName
     * @return Table|null
     */
    public function get($tableName)
    {
        if ($this->has($tableName) && !isset ($this->instatiated[$tableName]))
        {
            $this->instatiated[$tableName] = $this->callbacks[$tableName]();
        }

        return $this->instatiated[$tableName];
    }

    /**
     * Get all registered table names
     * @return array
     */
    public function getTableNames()
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
        foreach ($this->getTableNames() as $tableName)
        {
            $return[$tableName] = $this->get($tableName);
        }

        return $return;
    }
}
