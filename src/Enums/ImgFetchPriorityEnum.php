<?php

namespace Board3r\ResponsiveImage\Enums;

use Board3r\ResponsiveImage\Traits\EnumTrait;

enum ImgFetchPriorityEnum: string
{
    use EnumTrait;

    case HIGH = 'high';
    case LOW = 'low';
    case AUTO = 'auto';
}
