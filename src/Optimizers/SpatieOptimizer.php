<?php

namespace Board3r\ResponsiveImage\Optimizers;

use Board3r\ResponsiveImage\Interfaces\OptimizerInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Spatie\ImageOptimizer\OptimizerChainFactory;

/**
 * @see https://github.com/spatie/image-optimizer
 */
class SpatieOptimizer extends BaseOptimizer implements OptimizerInterface
{
    public function optimize(string $content, string $extension): string
    {
        if (in_array($extension, $this->getConfig('optimize_allowed', ['webp', 'jpg', 'png', 'gif']))) {
            $filename = uniqid('ri-tmp').'.'.$extension;
            $tmpFile = $this->generateTmpFileWithContent($filename, $content);
            $optimizerChain = OptimizerChainFactory::create();
            if ($this->getConfig('log', false)) {
                $optimizerChain->useLogger(Log::getLogger());
            }
            $optimizerChain->optimize($tmpFile->path($filename));
            $content = File::get($tmpFile->path($filename));
            $tmpFile->delete();
        }

        return $content;
    }
}
