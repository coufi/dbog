<?php
/**
 * dbog .../src/LoggerVoid.php
 */

namespace Src;

/**
 * Class LoggerVoid
 * @package Src
 *
 * This class is used as default debug behavior.
 */
class LoggerVoid extends Logger
{
    /**
     * {@inheritdoc}
     */
    protected function log($message)
    {
        // do nothing
    }
}
