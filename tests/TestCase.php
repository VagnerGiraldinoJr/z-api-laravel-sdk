<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \SuaEmpresa\ZApi\ZApiServiceProvider::class,
        ];
    }
}
