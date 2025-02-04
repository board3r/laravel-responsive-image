<?php

namespace Board3r\ResponsiveImage\Enums;

use Board3r\ResponsiveImage\Traits\EnumTrait;

enum CropOptionEnum: string
{
    use EnumTrait;

    case CENTER = 'center';
    case TOP = 'top';
    case TOP_RIGHT = 'top-right';
    case TOP_LEFT = 'top-left';
    case RIGHT = 'right';
    case BOTTOM = 'bottom';
    case BOTTOM_RIGHT = 'bottom-right';
    case BOTTOM_LEFT = 'bottom-left';
    case LEFT = 'left';
}
