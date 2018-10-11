<?php
/**
 * dbog .../src/Collection.php
 */

namespace Src;


use Src\Core\Entity;
use Src\Core\Schema;
use Src\Core\View;

class Collection
{
    /** @var Schema */
    protected $schema;

    /** @var \Closure[] */
    protected $callbacks = [];

    /** @var Table[]|View[] */
    protected $instatiated = [];

    /**
     * @param Schema $schema
     */
    public function __construct($schema)
    {
        $this->schema = $schema;
    }

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
        /** @var $className Entity */
        $itemName = $className::getLabel();

        $this->callbacks[$itemName] = function() use ($className) { return new $className($this->schema); };
    }

    /**
     * Get instance.
     * @param string $itemName
     * @return Table|View|null
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
     * @return Table[]|View[]
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
