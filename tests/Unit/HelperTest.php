<?php

namespace HerickBorgo\RestApi\Tests\Unit;

use HerickBorgo\RestApi\Tests\TestCase;

class HelperTest extends TestCase
{
    public function testUnexistsFunctionEnv()
    {
        self::assertFalse(function_exists('env'));
    }

    public function testExistsFunctionEnv()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'helper.php';
        self::assertTrue(function_exists('env'));
    }

    public function testUnexistsFunctionConfig()
    {
        self::assertFalse(!function_exists('config'));
    }

    public function testExistsFunctionConfig()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'helper.php';
        self::assertTrue(function_exists('config'));
    }
}
