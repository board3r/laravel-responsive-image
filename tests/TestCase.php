<?php

namespace Tests;

use Board3r\ResponsiveImage\ResponsiveImageServiceProvider;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use InteractsWithViews;

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
