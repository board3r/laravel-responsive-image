<?php

namespace Board3r\ResponsiveImage\Traits;

/**
 * Logic helper for Enums
 */
trait EnumTrait
{
    /**
     * Check if a given value is a valid enum case.
     */
    public static function isValid(string $type): bool
    {
        foreach (self::cases() as $case) {
            if ($case->value === $type) {
                return true;
            }
        }

        return false;
    }
}
