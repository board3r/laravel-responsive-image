<?php

namespace Tests\Feature;

use Board3r\ResponsiveImage\Support\ResponsiveImage;

test('Original Single Route', function () {
    $response = $this->get(ResponsiveImage::originPath('mountain.jpg'));
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/jpeg');
});

test('Original Single Route Not Found', function () {
    $response = $this->get(ResponsiveImage::originPath('fake.jpg'));
    $response->assertStatus(404);
});

test('Thumb Single Route', function () {
    $format = 'webp';
    $query = ['w' => 400, 'h' => 400, 'c' => null, 'f' => 'jpg'];
    $response = $this->get(ResponsiveImage::urlThumbPath().'/mountain.'.$format.'?'.http_build_query($query));
    $response->assertStatus(200);
});

test('Thumb Single Route Not Found', function () {
    $format = 'webp';
    $query = ['w' => 400, 'h' => 400, 'c' => null, 'f' => 'jpg'];
    $response = $this->get(ResponsiveImage::urlThumbPath().'/fake.'.$format.'?'.http_build_query($query));
    $response->assertStatus(404);
});

test('Thumbs Routes', function ($format, $width, $height, $crop, $ext) {
    $query = [];
    if (isset($width)) {
        $query['w'] = $width;
    }
    if (isset($height)) {
        $query['h'] = $height;
    }
    if (isset($crop)) {
        $query['c'] = $crop;
    }
    if (isset($ext)) {
        $query['f'] = $ext;
    }
    $response = $this->get(ResponsiveImage::urlThumbPath().'/mountain.'.$format.'?'.http_build_query($query));
    $response->assertStatus(200);
})->with('format available')->with('width')->with('height')->with('crop')->with('ext');
