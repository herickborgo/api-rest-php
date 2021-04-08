<?php

use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('env')) {
    /**
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    function env(string $key = '', string $default = null)
    {
        return getenv($key) ?: $default;
    }
}

if (!function_exists('config')) {
    /**
     * @param string $config
     * @return string|null
     */
    function config(string $config = '') {
        $index = strpos($config, '.');
        if ($index === false) {
            $index = strlen($config);
        }
        $file = substr_replace($config, '', $index, strlen($config));
        $key = substr_replace($config, '', 0, $index + 1);
        $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        $filename = sprintf('%s%s.php', $path, $file);
        $config = require($filename);
        return $config[$key];
    }
}

if (!function_exists('bcrypt')) {
    /**
     * @param string $str
     * @return string
     */
    function bcrypt(string $str): string
    {
        $options = [
            'cost' => 12,
        ];
        return password_hash($str, PASSWORD_BCRYPT, $options);
    }
}

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        foreach ($vars as $v) {
            VarDumper::dump($v);
        }

        exit(1);
    }
}
