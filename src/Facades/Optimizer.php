<?php

namespace Board3r\ResponsiveImage\Facades;

use Board3r\ResponsiveImage\Optimizers\SpatieOptimizer;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void optimize(string $content, string $extension)
 */
class Optimizer extends Facade
{
    public static function getFacadeAccessor()
    {
        return config('responsive-image.optimizer.class',SpatieOptimizer::class);
    }
}
