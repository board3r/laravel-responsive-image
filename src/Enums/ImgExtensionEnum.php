<?php

namespace Board3r\ResponsiveImage\Enums;

use Board3r\ResponsiveImage\Exceptions\NotAllowedValueException;
use Board3r\ResponsiveImage\Traits\EnumTrait;

enum ImgExtensionEnum: string
{
    use EnumTrait;

    case JPG = 'jpg';
    case JPEG = 'jpeg';
    case PNG = 'png';
    case WEBP = 'webp';
    case GIF = 'gif';

    /**
     * Test is the value is allowed in configuration
     *
     * @throws NotAllowedValueException
     */
    public static function isAllowedValue(string $value): bool
    {
        if (in_array($value, config('responsive-image.allowed_extension', []))) {
            return true;
        }

        return false;
    }
}
