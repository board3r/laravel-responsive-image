<?php

namespace Board3r\ResponsiveImage\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResponsiveImage
{
    protected static Filesystem $originDisk;

    protected static Filesystem $thumbDisk;

    /**
     * List all format of thumbnail existing on disk
     */
    public static function availableThumbFormat(): array
    {
        $thumbFilePath = self::thumbPath();
        $thumbFilePath = Str::replaceFirst('/', '', $thumbFilePath);

        $dirs = self::thumbDisk()->allDirectories($thumbFilePath);
        $sizes = [];
        foreach ($dirs as $dir) {
            $path = explode('/', $dir);
            $size = end($path);
            // tree dir, ignore
            if (strlen($size) <= 1) {
                continue;
            }
            if (isset($sizes[$size])) {
                $sizes[$size] = [];
            }
            // keep all directories for each size
            $sizes[$size][] = $dir;
        }

        return $sizes;
    }

    /**
     * Get the disk of origin images
     */
    public static function originDisk(): Filesystem
    {
        if (! isset(static::$originDisk)) {
            $disk = config('responsive-image.storage.origin', 'local');
            static::$originDisk = self::getDisk($disk);
        }

        return static::$originDisk;
    }

    /**
     * Get the disk of thumbnails
     */
    public static function thumbDisk(): Filesystem
    {
        if (! isset(static::$thumbDisk)) {
            $disk = config('responsive-image.storage.thumb', 'public');
            static::$thumbDisk = self::getDisk($disk);
        }

        return static::$thumbDisk;
    }

    protected static function getDisk($type): Filesystem
    {
        return Storage::disk($type);
    }

    /**
     * Store path of origin images
     */
    public static function originPath(?string $filepath = null): string
    {
        return config('responsive-image.img_path', '/responsive-image').(isset($filepath) ? '/'.$filepath : '');
    }

    /**
     * Store path of thumbnails
     */
    public static function thumbPath(?string $filepath = null): string
    {
        return config('responsive-image.img_thumb_path', '/responsive-image/thumbs').(isset($filepath) ? '/'.$filepath : '');
    }

    /**
     * Prefix used to route thumbnails
     */
    public static function urlThumbPath(?string $filepath = null): string
    {
        return config('responsive-image.url_path', '/img').(isset($filepath) ? '/'.$filepath : '');
    }

    /**
     * Generate thumbnail's path depending on the filename, filepath and query options
     */
    public static function thumbFilePath(string $filePath, string $filename, array $options): string
    {
        // determine the thumb directory
        $thumbDir = '';
        // create a tree for the thumb dir
        for ($i = 1; $i <= (int) config('responsive-image.thumb_dir_level', 1); $i++) {
            $thumbDir .= substr(md5($filePath.$filename), ($i - 1), 1).'/';
        }
        // add a final directory according to the format
        $thumbDir .= (isset($options['w']) ? 'w'.$options['w'] : '');
        $thumbDir .= (isset($options['h']) ? 'h'.$options['h'] : '');
        if (isset($options['w'], $options['h'], $options['c'])) {
            $thumbDir .= 'c'.$options['c'];
        }
        $thumbDir = Str::finish($thumbDir, '/');
        $thumbExt = $options['f'] ?? (string) config('responsive-image.default_thumb_ext', 'webp');

        return self::thumbPath($thumbDir.$filename.'.'.$thumbExt);
    }
}
