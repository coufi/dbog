<?php
/**
 * dbog .../test/TestSchema.php
 */

namespace Test;

use Src\Core\Schema As Schema;

class TestSchema extends Schema
{
    public function init()
    {
        $this->addTable(TestTableTemplate::class);
    }
}

