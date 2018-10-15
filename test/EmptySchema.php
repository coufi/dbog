<?php
/**
 * dbog .../test/EmptySchema.php
 */

namespace Test;

use Src\Core\Schema As Schema;

class EmptySchema extends Schema
{
    public function init()
    {
        // no table nor view added, empty schema
    }
}
