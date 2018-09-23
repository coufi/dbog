<?php
/**
 * dbog .../demo/TableContainer.php
 */

namespace Demo;

use Src\Core\TableContainer As Container;

class TableContainer extends Container
{
    public function init()
    {
        $this->add(Table\Order::class);
        $this->add(Table\OrderItem::class);
        $this->add(Table\Product::class);
    }
}

