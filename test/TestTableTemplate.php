<?php
/**
 * dbog .../test/TestTableTemplate.php
 */

namespace Test;


class TestTableTemplate extends \Src\Core\Table
{

    /**
     * {@inheritdoc }
     */
    protected function initConfiguration()
    {
        $config = $this->createConfig();
        return $config;
    }
}
