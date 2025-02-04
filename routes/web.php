<?php

use Board3r\ResponsiveImage\Http\Controllers\ResponsiveImageController;
use Board3r\ResponsiveImage\Support\ResponsiveImage;
use Illuminate\Support\Facades\Route;

// Catch all thumbs by a prefix and generated thumbs will have automatically header cache
Route::middleware('cache.headers:public;max_age='.config('responsive-image.cache_time', 60 * 60 * 24 * 30).';etag')->group(function () {
    Route::get(ResponsiveImage::urlThumbPrefix().'/{imgPath}', ResponsiveImageController::class)->where('imgPath', '.*');
    Route::get(ResponsiveImage::originPath().'/{imgPath}', function (string $imgPath) {
        ResponsiveImage::originPath($imgPath);
        $originalFilePath = ResponsiveImage::originPath($imgPath);
        if (ResponsiveImage::originDisk()->exists($originalFilePath)) {
            return ResponsiveImage::originDisk()->get($originalFilePath);
        } else {
            abort(404);
        }
    })->where('imgPath', '.*');
});
