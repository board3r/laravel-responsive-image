<?php

namespace Board3r\ResponsiveImage\Commands;

use Board3r\ResponsiveImage\Support\ResponsiveImage;
use Illuminate\Console\Command;

class ThumbCommand extends Command
{
    protected $signature = 'responsive-image:clean-thumbs {--f|format : Specify the format to clean. ex:w400h200}';

    protected $description = 'Clear cache size for responsive image';

    public const FORMAT_ALL = 'All';

    public function handle(): void
    {
        $format = $this->option('format');

        $sizes = ResponsiveImage::availableThumbFormat();
        $choices = array_keys($sizes);
        sort($choices);
        if (! $format) {
            $format = $this->choice(
                question: 'Select format to clear',
                choices: [self::FORMAT_ALL] + $choices,
                default: self::FORMAT_ALL,
            );
        }

        if ($format == 'All') {
            $format = $choices;
        }
        $nbDeleted = 0;
        foreach ($format as $f) {
            if (isset($sizes[$f])) {
                foreach ($sizes[$f] as $dir) {
                    ResponsiveImage::thumbDisk()->deleteDirectory($dir);
                    $nbDeleted++;
                }
            }
        }
        // clean empty directory
        if ($nbDeleted) {
            $this->info($nbDeleted.' directory cleared');
        } else {
            $this->info('No directory cleared');
        }
    }
}
