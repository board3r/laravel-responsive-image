<?php

namespace Board3r\ResponsiveImage\Commands;

use Board3r\ResponsiveImage\Traits\CacheTrait;
use Illuminate\Console\Command;

class CacheCommand extends Command
{
    use CacheTrait;

    protected $signature = 'responsive-image:clear-cache';

    protected $description = 'Clear cache size for responsive image';

    public function handle(): void
    {
        if (self::cache()->clear()) {
            $this->info('Cache cleared');
        } else {
            $this->error('Error clearing cache');
        }
    }
}
