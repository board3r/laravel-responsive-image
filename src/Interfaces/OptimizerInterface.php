<?php

namespace Board3r\ResponsiveImage\Interfaces;

interface OptimizerInterface
{
    /**
     * Optimize the image
     */
    public function optimize(string $content, string $extension): string;
}
