<?php

namespace Board3r\ResponsiveImage\Optimizers;

use Board3r\ResponsiveImage\Interfaces\OptimizerInterface;

class DisableOptimizer extends BaseOptimizer implements OptimizerInterface
{
    public function optimize(string $content, string $extension): string
    {
        return $content;
    }
}
