<?php

use Board3r\ResponsiveImage\Optimizers\SpatieOptimizer;
use Board3r\ResponsiveImage\Processors\InterventionProcessor;
use Intervention\Image\Drivers\Gd\Driver;

return [
    /**
     * Cache in second for the router to load image and thumbs
     */
    'cache_time' => env('RESPONSIVE_IMAGE_CACHE_TIME', 60 * 60 * 24 * 30),
    /**
     * Prefix used to use the logic to render thumbnails
     */
    'url_path' => env('RESPONSIVE_IMAGE_URL_PATH', '/img'),
    /**
     * Storage disk to save image and thumbs
     *
     * @see config/filesystems.php
     */
    'storage' => [
        'origin' => env('RESPONSIVE_IMAGE_STORAGE_ORIGIN', 'local'),
        'thumb' => env('RESPONSIVE_IMAGE_STORAGE_THUMB', 'public'),
    ],
    /**
     * Path in origin disk to store image
     */
    'img_path' => env('RESPONSIVE_IMAGE_IMG_PATH', '/responsive-image'),
    /**
     * Path in thumb disk to store thumbnails
     * You can clean thumbnails using : php artisan responsive-image:clean-thumbs
     * Possibilities to clean all or only format
     */
    'img_thumb_path' => env('RESPONSIVE_IMAGE_IMG_THUMB_PATH', '/responsive-image/thumbs'),
    /**
     * Allowed origin image's extension to generate thumbnails
     */
    'allowed_extension' => ['webp', 'jpg', 'jpeg', 'png', 'gif'],
    /**
     * Allowed thumbnails extension format
     */
    'allowed_format' => ['webp', 'jpg', 'png', 'gif'],
    /**
     * Default thumbnail extension, can be overridden by 'f' query parameter
     */
    'default_thumb_ext' => env('RESPONSIVE_IMAGE_DEFAULT_THUMB_EXT', 'webp'),
    /**
     * List of allowed crop position for thumbnail,use 'c' query parameter to define the thumbnail crop position
     */
    'allowed_crop' => ['center', 'top', 'top-right', 'top-left', 'left', 'bottom', 'bottom-right', 'bottom-left', 'right'],
    /**
     * List of allowed width for thumbnail, use 'w' query parameter to define the thumbnail width
     */
    'allowed_width' => [50, 100, 200, 400, 600, 800, 1200, 1400, 1600],
    /**
     * List of allowed height for thumbnail, use 'w' query parameter to define the thumbnail height
     */
    'allowed_height' => [50, 100, 200, 300, 400, 500, 600, 800, 1000, 1200],
    /**
     * Default pool of thumbnail's parameter
     */
    'default_srcset' => [['w' => 100], ['w' => 200], ['w' => 400], ['w' => 800], ['w' => 1200]],
    /**
     * The thumbnails are stored in a tree of directories, set here the number of level
     */
    'thumb_dir_level' => env('RESPONSIVE_IMAGE_THUMB_DIR_LEVEL', 2),
    /**
     * Processor settings - Used to resize, to crop and convert the original image
     */
    'processor' => [
        /**
         * Processor
         */
        'class' => InterventionProcessor::class,
        'intervention' => [
            /**
             * By default, you can use GD or Imagick
             */
            'driver' => Driver::class,
            /**
             * Settings for Image Intervention with GD Driver
             *
             * @see https://image.intervention.io/v3
             */
            'options' => [
                'autoOrientation' => true,
                'decodeAnimation' => true,
                'blendingColor' => 'ffffff',
                'strip' => false,
            ],
            'jpg' => ['quality' => 75, 'progressive' => false, 'strip' => false],
            'webp' => ['quality' => 75, 'strip' => false],
            'png' => ['interlaced' => false, 'indexed' => false],
            'gif' => ['interlaced' => false],
        ],
    ],
    /**
     * Optimizer settings
     */
    'optimizer' => [
        /**
         * Class used to optimize the size of image
         *
         * @see https://github.com/spatie/image-optimizer
         */
        'class' => SpatieOptimizer::class,
        'spatie' => [
            'optimize_allowed' => ['webp', 'jpg', 'png', 'gif'],
            'log' => false,
        ],
    ],
    /**
     * If the width and the height of the original picture cannot be determined, you can ignore them instead of generate an error
     * Better find a solution than change this parameter to TRUE
     */
    'ignore_miss_img_size' => false,
    /**
     * Define a specific cache to store calculated width and height of each original image
     * Your can clean this cache running : php artisan responsive-image:clear-cache
     */
    'cache' => [
        'driver' => 'file',
        'path' => storage_path('framework/cache/responsive-image'),
    ],
];
