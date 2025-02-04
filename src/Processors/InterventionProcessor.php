<?php

namespace Board3r\ResponsiveImage\Processors;

use Board3r\ResponsiveImage\Interfaces\ProcessorInterface;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * @see https://intervention.io/
 */
class InterventionProcessor extends BaseProcessor implements ProcessorInterface
{
    protected ImageInterface|EncodedImageInterface $object;

    protected function getObject(): ImageInterface|EncodedImageInterface
    {
        if (! isset($this->object)) {
            $manager = new ImageManager(
                $this->getConfig('driver', Driver::class),
                autoOrientation: $this->getConfig('options.autoOrientation', true),
                decodeAnimation: $this->getConfig('options.decodeAnimation', true),
                blendingColor: $this->getConfig('options.blendingColor', 'ffffff'),
                strip: $this->getConfig('options.strip', false)
            );
            $this->object = $manager->read($this->fileContent);
        }

        return $this->object;
    }

    protected function setObj(ImageInterface|EncodedImageInterface $obj): ImageInterface|EncodedImageInterface
    {
        $this->object = $obj;

        return $this->object;
    }

    public function crop(int $width, int $height, string $position = 'center'): void
    {
        $this->getObject()->cover($width, $height, $position);
    }

    public function scaleX(int $width): void
    {
        $this->getObject()->scale(width: $width);
    }

    public function scaleY(int $height): void
    {
        $this->getObject()->scale(height: $height);
    }

    public function encodeFormat(string $format): void
    {
        $thumb = match ($format) {
            'webp' => $this->getObject()->toWebp((int) $this->getConfig('webp.quality', 75), (bool) $this->getConfig('webp.strip', false)),
            'jpg' => $this->getObject()->toJpeg((int) $this->getConfig('jpg.quality', 75), (bool) $this->getConfig('jpg.progressive', false),
                (bool) $this->getConfig('jpg.strip', false)),
            'png' => $this->getObject()->toPng((bool) $this->getConfig('png.interlaced', false), (bool) $this->getConfig('png.indexed', false)),
            'gif' => $this->getObject()->toGif((bool) $this->getConfig('gif.interlaced', false)),
        };
        $this->setObj($thumb);
    }

    public function getThumbContent(): string
    {
        return $this->getObject()->toString();
    }
}
