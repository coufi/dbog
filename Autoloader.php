<?php
/**
 * dbog .../Autoloader.php
 */

class Autoloader
{
    const FILE_EXTENSION = '.php';

    public function init()
    {
        spl_autoload_register(function ($className)
        {

            // revert backslashes in full class name
            $path = str_replace('\\', DIRECTORY_SEPARATOR, $className) .  self::FILE_EXTENSION;
            $fileName = basename($path);
            $dirPath = strtolower(dirname($path));
            $filePath = __DIR__ . DIRECTORY_SEPARATOR . $dirPath . DIRECTORY_SEPARATOR . $fileName;

            if (file_exists($filePath))
            {
                require_once $filePath;
            }

        });
    }

}