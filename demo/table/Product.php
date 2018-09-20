<?php
/**
 * dbog .../demo/table/Product.php
 */

namespace Demo\Table;


class Product extends \Src\Core\Table
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