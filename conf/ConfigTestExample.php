<?php
/**
 * dbog .../conf/ConfigTetExample.php
 * Can be renamed to ConfigTest.php, edited and used as configuration file for syncer tests
 */

namespace Conf;


class ConfigTestExample extends \Src\Config
{
    public function __construct()
    {
        // config db connection
        $server = $this->createDbServerConfig(
            self::DRIVER_MYSQL,
            'localhost'
        );

        $this->addInstance(
            $server,
            'root',
            'pwdtestexample',
            'sync_test_schema'
        )->setSchema($this->createSchema(\Demo\Schema::class));
    }
}
