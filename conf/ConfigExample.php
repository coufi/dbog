<?php
/**
 * dbog .../conf/ConfigExample.php
 * Can be renamed to Config.php, edited and used as application configuration
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
            'root',
            'pwdexample',
            'sync_schema'
        )->setSchema($this->createSchema(\Demo\Schema::class));
    }
}
