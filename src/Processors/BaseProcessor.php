<?php

namespace Board3r\ResponsiveImage\Processors;

class BaseProcessor
{
    /**
     * @var string store raw data of the file
     */
    protected string $fileContent;

    public function setFileContent(string $content): void
    {
        $this->fileContent = $content;
    }

    public function getFileContent(): string
    {
        return $this->fileContent;
    }

    /**
     * Get Config for the current processor
     *
     * @return array
     */
    protected function getConfig(string $key, mixed $default): mixed
    {
        return config('responsive-image.processor.'.str_replace('processor', '', strtolower(static::class)).'.'.$key, $default);
    }
}
