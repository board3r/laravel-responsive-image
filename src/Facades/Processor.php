<?php

namespace Board3r\ResponsiveImage\Facades;

use Board3r\ResponsiveImage\Optimizers\SpatieOptimizer;
use Board3r\ResponsiveImage\Processors\InterventionProcessor;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void setFileContent(string $content)
 * @method static string getFileContent()
 * @method static string getThumbContent()
 * @method static void crop(int $width, int $height, string $position = 'center')
 * @method static void scaleX(int $width)
 * @method static void scaleY(int $height)
 * @method static void encodeFormat(string $format)
 */
class Processor extends Facade
{
    public static function getFacadeAccessor()
    {
        return config('responsive-image.processor.class',InterventionProcessor::class);
    }
}
