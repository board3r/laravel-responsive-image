<?php

namespace Board3r\ResponsiveImage\Traits;

use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    public static function cache(): Repository
    {
        static $cache;
        if (! isset($cache)) {
            $cache = Cache::build(config('responsive-image.cache',
                ['driver' => 'file', 'path' => storage_path('framework/cache/responsive-image')]
            ));
        }

        return $cache;
    }
}
