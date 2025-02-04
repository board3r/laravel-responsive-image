<?php

namespace Board3r\ResponsiveImage\Interfaces;

/**
 * Process an image to generate a thumbnail
 */
interface ProcessorInterface
{
    /**
     * Set the original image content
     */
    public function setFileContent(string $content): void;

    /**
     * Get the original image content
     */
    public function getFileContent(): string;

    /**
     * Get the thumb generated content
     */
    public function getThumbContent(): string;

    /**
     * Crop the image
     */
    public function crop(int $width, int $height, string $position = 'center'): void;

    /**
     * Scale image on the width
     */
    public function scaleX(int $width): void;

    /**
     * Scale the image on the height
     */
    public function scaleY(int $height): void;

    /**
     * Encode the image in specific format
     */
    public function encodeFormat(string $format): void;
}
