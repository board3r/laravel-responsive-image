<?php

namespace Board3r\ResponsiveImage\Http\Controllers;

use Board3r\ResponsiveImage\Enums\ImgExtensionEnum;
use Board3r\ResponsiveImage\Enums\ThumbUrlQueryEnum;
use Board3r\ResponsiveImage\Interfaces\OptimizerInterface;
use Board3r\ResponsiveImage\Interfaces\ProcessorInterface;
use Board3r\ResponsiveImage\Optimizers\SpatieOptimizer;
use Board3r\ResponsiveImage\Processors\InterventionProcessor;
use Board3r\ResponsiveImage\Support\ResponsiveImage;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

/**
 * Generate and optimize a thumbnail
 */
class ResponsiveImageController extends Controller
{
    public function __invoke(Request $request, string $imgPath): Response
    {
        // to process a thumb, the file must be recorded in the storage configured
        $originalFilePath = ResponsiveImage::originPath($imgPath);
        $infos = pathinfo($originalFilePath);
        $fileExt = $infos['extension'];
        // only allow to process images
        if (! ImgExtensionEnum::isAllowedValue($fileExt)) {
            abort(404);
        }

        $filePath = Str::lower(realpath($infos['dirname']));
        $filename = Str::Slug(Str::lower($infos['filename']));
        $options = $this->getRequestOptions($request);

        // if none parameters return original file
        if (! $options) {
            if (! ResponsiveImage::originDisk()->exists($originalFilePath)) {
                abort(404);
            }

            return $this->sendResponse(ResponsiveImage::originDisk(), $originalFilePath);
        } else {
            $thumbFilePath = ResponsiveImage::thumbFilePath($filePath, $filename, $options);
            $thumbExt = pathinfo($thumbFilePath, PATHINFO_EXTENSION);
            // if the thumb not already exist, create it
            if (! ResponsiveImage::thumbDisk()->exists($thumbFilePath)) {
                // to create the thumb, the original image must exist
                if (! ResponsiveImage::originDisk()->exists($originalFilePath)) {
                    abort(404);
                }
                /**
                 * @var ProcessorInterface $processor
                 */
                $processor = new (config('responsive-image.processor.class', InterventionProcessor::class));
                $processor->setFileContent(ResponsiveImage::originDisk()->get($originalFilePath));
                if (isset($options['w'], $options['h'])) {
                    $processor->crop($options['w'], $options['h'], $options['c'] ?? 'center');
                } else {
                    if (isset($options['w'])) {
                        $processor->scaleX($options['w']);
                    } else {
                        if (isset($options['h'])) {
                            $processor->scaleY($options['h']);
                        }
                    }
                }
                $processor->encodeFormat($thumbExt);
                $thumbContent = $processor->getThumbContent();
                /**
                 * @var OptimizerInterface $optimizer
                 */
                $optimizer = new (config('responsive-image.optimizer.class', SpatieOptimizer::class));
                $thumbContent = $optimizer->optimize($thumbContent, $thumbExt);
                ResponsiveImage::thumbDisk()->put($thumbFilePath, $thumbContent);
            }

            return $this->sendResponse(ResponsiveImage::thumbDisk(), $thumbFilePath);
        }
    }

    /**
     * Set the options to process the thumb from the query parameters
     */
    protected function getRequestOptions(Request $request): array
    {
        $options['w'] = (int) $request->query('w');
        $options['h'] = (int) $request->query('h');
        $options['c'] = $request->query('c');
        $options['f'] = $request->query('f');

        foreach ($options as $option => $value) {
            if (! $value) {
                unset($options[$option]);

                continue;
            }
            if (ThumbUrlQueryEnum::isDefaultValue($option, $value)) {
                unset($options[$option]);
            } else {
                if (! ThumbUrlQueryEnum::isAllowedValue($option, $value)) {
                    abort(404);
                }
            }
        }

        return $options;
    }

    protected function sendResponse(Filesystem $disk, string $filePath): Response
    {
        $content = $disk->get($filePath);
        $mimeType = $disk->mimeType($filePath);
        try {
            return response()->make($content)->header('Content-Type', $mimeType);
        } catch (Exception) {
            abort(500);
        }
    }
}
