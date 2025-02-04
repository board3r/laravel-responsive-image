<?php

namespace Board3r\ResponsiveImage;

use Board3r\ResponsiveImage\Commands\CacheCommand;
use Board3r\ResponsiveImage\Commands\ThumbCommand;
use Board3r\ResponsiveImage\View\Components\ResponsiveImage;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ResponsiveImageServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('responsive-image')
            ->hasConfigFile()
            ->hasViews('board3r')
            ->hasViewComponent('board3r', ResponsiveImage::class)
            ->hasRoute('web')
            ->hasCommands(CacheCommand::class, ThumbCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->publishConfigFile();
            });
    }
}
