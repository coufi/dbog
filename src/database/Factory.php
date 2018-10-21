<?php
/**
 * dbog .../src/database/Factory.php
 */

namespace Src\Database;

class Factory
{

    /**
     * Create new db adapter instance.
     * @param Instance $instance
     * @return AdapterInterface
     * @throws \Exception
     */
    public function create($instance)
    {
        $db = $this->getAdapterInstance($instance->getDbAdapter());
        $db->connect($instance);

        return $db;
    }

    /**
     * @param string $name
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getAdapterInstance($name)
    {
        $methodName = 'getAdapter' . $name;
        if (!method_exists($this, $methodName))
        {
            throw new \Exception("Cannot instantiate DB adapter {$name}");
        }

        return $this->$methodName();
    }

    /**
     * @return AdapterPDO
     */
    private function getAdapterPDO()
    {
        return new AdapterPDO();
    }
}
