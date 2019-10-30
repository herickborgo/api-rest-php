<?php

class Autoload
{
    /**
     * @var array
     **/
    private $config;

    /**
     * @var array
     **/
    private $importedClasses;

    public function __construct()
    {
        $this->importedClasses = [];
        $this->config = include 'autoload_config.php';
        spl_autoload_extensions('.php');
        spl_autoload_register([$this, 'load']);
    }

    private function load($className)
    {
        $mapperPackage = $this->config['alias'];
        foreach ($mapperPackage as $key => $value) {
            $extension = spl_autoload_extensions('.php');
            $className = str_replace($value, $key, $className);
            $classNameReplace = str_replace('\\', DIRECTORY_SEPARATOR, $className);
            if (strpos($classNameReplace, $key) !== false) {
                require_once(__DIR__ . DIRECTORY_SEPARATOR . $classNameReplace . $extension);
            }
        }
    }

    public static function loadClass()
    {
        new self();
    }
}

Autoload::loadClass();
