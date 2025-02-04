<?php

namespace Board3r\ResponsiveImage\View\Components;

use Board3r\ResponsiveImage\Enums\ImgDecodingEnum;
use Board3r\ResponsiveImage\Enums\ImgExtensionEnum;
use Board3r\ResponsiveImage\Enums\ImgFetchPriorityEnum;
use Board3r\ResponsiveImage\Enums\ImgLoadingEnum;
use Board3r\ResponsiveImage\Enums\ThumbUrlQueryEnum;
use Board3r\ResponsiveImage\Exceptions\InvalidComponentParameterException;
use Board3r\ResponsiveImage\Exceptions\InvalidQueryParameterException;
use Board3r\ResponsiveImage\Support\ResponsiveImage as ResponsiveImageSupport;
use Board3r\ResponsiveImage\Traits\CacheTrait;
use cardinalby\ContentDisposition\ContentDisposition;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

/**
 * Component to render img HTML tag with responsive attributes
 */
class ResponsiveImage extends Component
{
    use CacheTrait;

    public false|string $loading = 'lazy';

    public false|string $fetchPriority = false;

    public false|string $decoding = false;

    /**
     * Unique image identification
     */
    protected string $imageMd5;

    /**
     * Create a new component instance.
     *
     * @param  string  $image  The original image
     * @param  array  $thumbs  Can send and array of query params for the thumb processor like [['w'=>400,'c'=>'top'],['w'=>200,'h'=>100],...]
     *                         or if the user set to true the param $useCustomThumbs and array of thumb url like ['/medium-picture.webp 400w','/large-picture.webp 800w','https:///my-website.com/low-picture.png 200w']
     *                         in the last case it's important to send information of width after the url to keep responsive aspect in the srcset
     * @param  string  $width  Attribute 'width' information of the original image, if this information is not filled, it will be calculated depending on the file
     * @param  string  $height  Attribute 'height' information of the original image, if this information is not filled, it will be calculated depending on the file
     * @param  false|ImgLoadingEnum  $loading  Attribute 'loading' for the img HTML tag
     * @param  false|ImgFetchPriorityEnum  $fetchPriority  Attribute 'fetchPriority' for the img HTML tag
     * @param  false|ImgDecodingEnum  $decoding  Attribute 'decoding' for the img HTML tag
     * @param  bool  $useCustomImage  If enabled the main image must be a public url to the file and the main image will not be processed
     * @param  bool  $useCustomThumbs  Enable custom thumbs. See $thumbs param
     *
     * @throws InvalidComponentParameterException
     */
    public function __construct(
        public string $image,
        public array $thumbs = [],
        public string $width = '',
        public string $height = '',
        string|false $loading = 'lazy',
        string|false $fetchPriority = '',
        string|false $decoding = '',
        protected bool $useCustomImage = false,
        protected bool $useCustomThumbs = false
    ) {

        if ($loading) {
            $this->loading = ImgLoadingEnum::tryFrom($loading)->value;
        }
        if (is_string($fetchPriority)) {
            $this->fetchPriority = ImgFetchPriorityEnum::tryFrom($fetchPriority)->value;
        }
        if ($decoding) {
            $this->loading = ImgDecodingEnum::tryFrom($decoding)->value;
        }

        $this->fetchPriority = ImgFetchPriorityEnum::tryFrom($this->fetchPriority);
        $this->decoding = ImgDecodingEnum::tryFrom($this->decoding);

        $this->imageMd5 = md5($this->image);
        // if no thumbs information, use the default params
        if (! $this->thumbs) {
            $this->thumbs = (array) config('responsive-image.default_srcset');
        }
        // if the image got a protocol, force custom image
        if (Str::isUrl($this->image)) {
            $this->useCustomImage = true;
        }
        if ($this->useCustomImage) {
            // if custom image no start with protocol means it s a local image, just get the full route instead
            if (! Str::isUrl($this->image)) {
                $this->image = route($this->image);
            }
            // save the image in local
            $filename = $this->getFilenameFromUrl($this->image);

            // copy the image in local
            $originalFilePath = ResponsiveImageSupport::originPath($filename);
            if (! ResponsiveImageSupport::originDisk()->exists($originalFilePath)) {
                $imgContent = file_get_contents($this->image);
                ResponsiveImageSupport::originDisk()->put($originalFilePath, $imgContent);
            }
            $this->image = $filename;
        }
        $this->setSize();
    }

    /**
     * @throws InvalidComponentParameterException
     */
    protected function setSize(): void
    {
        // if no width or height information, calculate the information from the original image
        if (! config('responsive-image.ignore_miss_img_size', false)) {
            if (! $this->width || ! $this->height) {
                if (self::cache()->has($this->imageMd5)) {
                    $cacheData = self::cache()->get($this->imageMd5, []);
                    $this->width = $cacheData['width'];
                    $this->height = $cacheData['height'];
                } else {
                    $originalFilePath = ResponsiveImageSupport::originPath($this->image);
                    if (ResponsiveImageSupport::originDisk()->exists($originalFilePath)) {
                        $imgInfo = getimagesizefromstring(ResponsiveImageSupport::originDisk()->get($originalFilePath));
                        $this->width = $imgInfo[0];
                        $this->height = $imgInfo[1];
                    }
                    if ($this->width && $this->height) {
                        self::cache()->forever($this->imageMd5, ['width' => $this->width, 'height' => $this->height]);
                    }
                }
            }
            // check if the size is missing
            if ((! is_numeric($this->width) || ! is_numeric($this->height))) {
                throw new InvalidComponentParameterException('Width and height must be numeric');
            }
        }
    }

    /**
     * Return the prefix used for thumb
     */
    protected function getUrlThumbPath(): string
    {
        static $thumbPath;
        if (! isset($thumbPath)) {
            // no add prefix if the user send his own thumbnails
            $thumbPath = ($this->useCustomThumbs ? '' : ResponsiveImageSupport::urlThumbPrefix().'/');
        }

        return $thumbPath;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('board3r::components.responsive-image', [
            'src' => ResponsiveImageSupport::originPath($this->image),
            'srcset' => $this->formatSrcset(),
            'width' => $this->width,
            'height' => $this->height,
            'loading' => $this->loading,
            'fetchPriority' => $this->fetchPriority,
            'decoding' => $this->decoding,
        ]);
    }

    /**
     * Generate and format the attribute 'srcset'
     */
    protected function formatSrcset(): string
    {
        $return = [];
        // use direct information if custom
        if ($this->useCustomThumbs) {
            $return = $this->thumbs;
        } else {
            // or generate the srcset
            foreach ($this->thumbs as $thumbParams) {
                try {
                    foreach ($thumbParams as $thumbParam => $value) {
                        // check is the query param is valid
                        if (! ThumbUrlQueryEnum::isValid($thumbParam)) {
                            unset($thumbParams[$thumbParam]);

                            continue;
                        }
                        // check is the query value is not a default
                        if (ThumbUrlQueryEnum::isDefaultValue($thumbParam, $value)) {
                            unset($thumbParams[$thumbParam]);
                            // check is the query value is allowed in configuration
                        } elseif (! ThumbUrlQueryEnum::isAllowedValue($thumbParam, $value)) {
                            throw new InvalidQueryParameterException($value.' is not allowed for the param '.$thumbParam);
                        }
                    }
                } catch (InvalidQueryParameterException) {
                    continue;
                }
                if (! config('responsive-image.ignore_miss_img_size', false)) {
                    // if the thumb's width or height are bigger than the original image, we will not add this thumb
                    if ((isset($thumbParams['w']) && $thumbParams['w'] > $this->width) || (isset($thumbParams['h']) && $thumbParams['h'] > $this->height)) {
                        continue;
                    }
                }
                if ($this->useCustomImage) {
                    $image = basename($this->image);
                } else {
                    $image = $this->image;
                }
                // if the width or the height of the original image are not set, use a fake default 800px.  @todo how to do well ?
                $thumbWidth = (int) (
                    $thumbParams['w'] ??
                    (isset($thumbParams['h']) ? (((int) $this->width ?: 800) * (int) $thumbParams['h'] / ((int) $this->height ?: 800)) : (int) $this->width)
                );
                // if the thumbnail is equal or bigger than the original, no use it
                if ($thumbWidth >= $this->width) {
                    continue;
                }
                $return[$thumbWidth] = $this->getUrlThumbPath().$image.'?'.http_build_query($thumbParams).' '.$thumbWidth.'w';
            }
        }
        // largest thumb first
        krsort($return);

        return implode(', ', $return);
    }

    /**
     * Get a path and a name for external ressource
     */
    protected function getFilenameFromUrl($url): false|string
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? 'external';
        $ext = '';
        $filename = '';
        if (isset($parsedUrl['path'])) {
            if ($ext = pathinfo($parsedUrl['path'], PATHINFO_EXTENSION)) {
                $filename = Str::replace(['/', '\\', '_', ' '], '-', $parsedUrl['path']);
            } else {
                $context = stream_context_create([
                    'http' => [
                        'follow_location' => true,
                    ],
                ]);
                $headers = get_headers($url, true, $context);
                if (isset($headers['Content-Disposition'])) {
                    $filename = ContentDisposition::parse($headers['Content-Disposition'])->getFilename();
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                }
            }
        }
        if ($ext && ! ImgExtensionEnum::isAllowedValue($ext)) {
            return false;
        }
        $filename = Str::replaceLast('.'.$ext, '', $filename);

        return $host.'/'.Str::lower($filename.'.'.$ext);
    }
}
