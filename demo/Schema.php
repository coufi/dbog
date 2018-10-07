<?php
/**
 * dbog .../demo/Schema.php
 */

namespace Demo;

use Src\Core\Schema As DbSchema;

class Schema extends DbSchema
{
    public function init()
    {
        $this->addTable(Table\Order::class);
        $this->addTable(Table\OrderItem::class);
        $this->addTable(Table\Product::class);
    }
}

