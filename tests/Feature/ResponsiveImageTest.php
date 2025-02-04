<?php

namespace Tests\Feature;

use Board3r\ResponsiveImage\Commands\ThumbCommand;
use Board3r\ResponsiveImage\Support\ResponsiveImage;
use Board3r\ResponsiveImage\View\Components\ResponsiveImage as Component;

test('Testing environment data', function (string $format) {
    $image = ResponsiveImage::originPath('mountain.'.$format);
    expect(ResponsiveImage::originDisk()->exists($image))->toBeTrue(ResponsiveImage::originDisk()->path(ResponsiveImage::originPath($image))." doesn't exist");
})->with('format available');

test('Simple component', function () {
    $component = new Component(
        image: 'mountain.jpg',
        thumbs: [['w' => 200, 'h' => 200]],
    );
    expectComponent($component);
});

test('Generate components', function ($thumbs) {
    $component = new Component(
        image: 'mountain.jpg',
        thumbs: $thumbs,
    );
    expectComponent($component);
})->with('thumbs');

test('Component from external image', function () {
    $component = new Component(
        image: 'https://picsum.photos/id/450/1600/900',
        thumbs: [['w' => 200, 'h' => 200]]
    );
    expectComponent($component);
});

test('Original Single Route', function () {
    $response = $this->get(ResponsiveImage::originPath('mountain.jpg'));
    $response->assertStatus(200);
});

test('Original Single Route Not Found', function () {
    $response = $this->get(ResponsiveImage::originPath('fake.jpg'));
    $response->assertStatus(404);
});

test('Thumb Single Route', function () {
    $format = 'webp';
    $query = ['w' => 400, 'h' => 400, 'c' => null, 'f' => 'jpg'];
    $response = $this->get(ResponsiveImage::urlThumbPrefix().'/mountain.'.$format.'?'.http_build_query($query));
    $response->assertStatus(200);
});

test('Thumb Single Route Not Found', function () {
    $format = 'webp';
    $query = ['w' => 400, 'h' => 400, 'c' => null, 'f' => 'jpg'];
    $response = $this->get(ResponsiveImage::urlThumbPrefix().'/fake.'.$format.'?'.http_build_query($query));
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
    $response = $this->get(ResponsiveImage::urlThumbPrefix().'/mountain.'.$format.'?'.http_build_query($query));
    $response->assertStatus(200);
})->with('format available')->with('width')->with('height')->with('crop')->with('ext');

test('Console Cache', function () {
    $this->artisan('responsive-image:clear-cache')
        ->assertExitCode(0);
});

test('Console Thumbs Clean', function () {
    $sizes = ResponsiveImage::availableThumbFormat();
    $choices = array_keys($sizes);
    sort($choices);
    $this->artisan('responsive-image:clean-thumbs')
        ->expectsChoice('Select format to clear', ThumbCommand::FORMAT_ALL, [ThumbCommand::FORMAT_ALL] + $choices)
        ->expectsOutputToContain('directory cleared')
        ->assertExitCode(0);
});
