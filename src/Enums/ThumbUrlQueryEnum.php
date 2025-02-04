<?php

namespace Board3r\ResponsiveImage\Enums;

use Board3r\ResponsiveImage\Exceptions\NotAllowedValueException;
use Board3r\ResponsiveImage\Traits\EnumTrait;

enum ThumbUrlQueryEnum: string
{
    use EnumTrait;

    case WIDTH = 'w';
    case HEIGHT = 'h';
    case CROP = 'c';
    case FORMAT = 'f';

    /**
     * Test is the value is allowed in configuration
     *
     * @throws NotAllowedValueException
     */
    public static function isAllowedValue(string $thumbParam, string $value): bool
    {
        if (in_array($value, self::getConfAllowed($thumbParam))) {
            return true;
        }

        return false;
    }

    /**
     * Load the configuration according to the attribute
     *
     * @throws NotAllowedValueException
     */
    public static function getConfAllowed(string $query): array
    {
        return match ($query) {
            self::WIDTH->value => config('responsive-image.allowed_width', []),
            self::HEIGHT->value => config('responsive-image.allowed_height', []),
            self::CROP->value => config('responsive-image.allowed_crop', []),
            self::FORMAT->value => config('responsive-image.allowed_format', []),
            default => throw new NotAllowedValueException('Invalid query parameter'),
        };
    }

    /**
     * Check if the value can be a default value for the query
     */
    public static function isDefaultValue(string $thumbParam, string $value): bool
    {
        if (! $value ||
            ($thumbParam == 'c' && $value == CropOptionEnum::CENTER->value) ||
            ($thumbParam == 'f' && $value == config('responsive-image.default_thumb_ext', ImgExtensionEnum::WEBP->value))
        ) {
            return true;
        }

        return false;
    }
}
