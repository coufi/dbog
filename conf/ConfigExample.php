<?php
/**
 * dbog ...conf/ConfigExample.php
 * Cab be renamed to Config.php and used as application configuration
 */

namespace Conf;


class ConfigExample extends \Src\Config
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
            'sync_test',
            'root',
            'pwdexample',
            new \Demo\TableContainer()
        );
    }
}