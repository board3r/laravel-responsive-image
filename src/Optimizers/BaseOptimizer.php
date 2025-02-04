<?php

namespace Board3r\ResponsiveImage\Optimizers;

use Illuminate\Support\Facades\File;
use Spatie\TemporaryDirectory\Exceptions\PathAlreadyExists;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class BaseOptimizer
{
    protected function generateTmpFileWithContent(string $filename, string $content): TemporaryDirectory
    {
        try {
            $tmpDir = (new TemporaryDirectory)
                ->create();
            File::put($tmpDir->path($filename), $content);

            return $tmpDir;
        } catch (PathAlreadyExists) {
            // retry on error
            return $this->generateTmpFileWithContent($filename, $content);
        }
    }

    /**
     * Get Config for the current optimizer
     *
     * @return array
     */
    protected function getConfig(string $key, mixed $default): mixed
    {
        return config('responsive-image.optimizer.'.str_replace('optimizer', '', strtolower(static::class)).'.'.$key, $default);
    }
}
