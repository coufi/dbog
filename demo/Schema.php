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
        $this->addTable(Table\Content::class);
        $this->addTable(Table\Country::class);
        $this->addTable(Table\Currency::class);
        $this->addTable(Table\DeliveryType::class);
        $this->addTable(Table\Menu::class);
        $this->addTable(Table\MenuItem::class);
        $this->addTable(Table\MenuItemContent::class);
        $this->addTable(Table\MenuItemProductCategory::class);
        $this->addTable(Table\MenuItemUrl::class);
        $this->addTable(Table\Order::class);
        $this->addTable(Table\OrderItem::class);
        $this->addTable(Table\PaymentType::class);
        $this->addTable(Table\Product::class);
        $this->addTable(Table\ProductCategory::class);
        $this->addTable(Table\ProductHasProductCategory::class);

        $this->addView(View\SalesOverview::class);
    }
}

