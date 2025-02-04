<?php

namespace Board3r\ResponsiveImage\Enums;

use Board3r\ResponsiveImage\Traits\EnumTrait;

enum ImgDecodingEnum: string
{
    use EnumTrait;

    case SYNC = 'sync';
    case ASYNC = 'async';
    case LAZY = 'lazy';
}
