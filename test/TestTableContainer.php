<?php
/**
 * dbog .../demo/TestTableContainer.php
 */

namespace Test;

use Src\Core\TableContainer As Container;

class TestTableContainer extends Container
{
    public function init()
    {
        $this->add(TestTableTemplate::class);
    }
}

