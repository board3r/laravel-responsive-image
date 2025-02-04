<?php

namespace Board3r\ResponsiveImage\Enums;

use Board3r\ResponsiveImage\Traits\EnumTrait;

enum ImgLoadingEnum: string
{
    use EnumTrait;

    case AUTO = 'auto';
    case LAZY = 'lazy';
    case EAGER = 'eager';
}
