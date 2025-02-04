<?php

namespace Tests;

use Board3r\ResponsiveImage\ResponsiveImageServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ResponsiveImageServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        config()->set('filesystems.disks.local.root', __DIR__.'/fixtures');
    }
}
