<?php
/**
 * dbog .../demo/view/SalesOverview.php
 */

namespace Demo\View;


use Src\Core\View\Config;

class SalesOverview extends \Src\Core\View
{
    public function getConfiguration()
    {
        $query = "select `order_item`.`id_product` AS `id_product`,sum(`order_item`.`quantity`) AS `sold_quantity` from `order_item` where `order_item`.`canceled` = 0 group by `order_item`.`id_product`";
        $config = new Config($this->viewName, $query);

        return $config;
    }
}
