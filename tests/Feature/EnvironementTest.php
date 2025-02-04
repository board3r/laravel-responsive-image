<?php

namespace Tests\Feature;

use Board3r\ResponsiveImage\Support\ResponsiveImage;

test('Testing environment data', function (string $format) {
    $image = ResponsiveImage::originPath('mountain.'.$format);
    expect(ResponsiveImage::originDisk()->exists($image))->toBeTrue(ResponsiveImage::originDisk()->path(ResponsiveImage::originPath($image))." doesn't exist");
})->with('format available');
