<?php

namespace HerickBorgo\RestApi\Infrastructure\Environment;

final class Environment
{
    public static function load(string $path, string $envfile = '.env')
    {
        $env = file_get_contents($path . DIRECTORY_SEPARATOR . $envfile);
        $environments = self::createArray($env);
        foreach ($environments as $setting) {
            putenv($setting);
        }
    }

    private static function createArray(string $content = ''): array
    {
        $variables = preg_split('/\s+/', $content);
        $variables = array_filter($variables);
        return $variables;
    }
}
