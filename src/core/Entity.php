<?php
/**
 * dbog .../src/core/Entity.php
 */

namespace Src\Core;

use \Src\Core as Core;

abstract class Entity
{
    /** @var Schema */
    protected $schema;

    /**
     * @param Schema $schema
     */
    public function __construct($schema)
    {
        $this->schema = $schema;
    }

    /**
     * Get schema.
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Get entity configuration.
     * @return Core\Table\Config|Core\View\Config
     */
    public abstract function getConfiguration();

    /**
     * Get entity name without class instantiating.
     * @return string
     */
    public static function getLabel()
    {
        // remove namespace string from called class
        $className = explode('\\', get_called_class());
        $className = end( $className);

        // convert camelcase class name to snake case entity name
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $className)), '_');
    }
}
