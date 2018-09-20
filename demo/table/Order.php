<?php
/**
 * dbog .../demo/table/Order.php
 */

namespace Demo\Table;


class Order extends \Src\Core\Table
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