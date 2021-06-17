<?php

namespace Blimundo\Menu\Tests;

use Blimundo\Menu\MenuServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling([
            \Illuminate\Validation\ValidationException::class,
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            MenuServiceProvider::class,
        ];
    }
}
