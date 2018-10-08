<?php
/**
 * dbog .../src/core/view/Config.php
 */

namespace Src\Core\View;

class Config
{
    /**  @var string */
    protected $viewName;

    /**  @var string */
    protected $query;

    /**
     * @param string $viewName
     * @param string $query
     */
    public function __construct($viewName, $query)
    {
        $this->viewName = $viewName;
        $this->query = $query;
    }

    /**
     * Get view name.
     * @return string
     */
    public function getName()
    {
        return $this->viewName;
    }

    /**
     * Get view query.
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
}
