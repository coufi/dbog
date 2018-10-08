<?php
/**
 * dbog .../src/core/View.php
 */

namespace Src\Core;

abstract class View extends Entity
{
    /** @var string */
    protected $viewName;

    public function __construct()
    {
        $this->viewName = self::getLabel();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->viewName;
    }
}