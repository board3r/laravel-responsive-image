<?php

namespace Tests\Feature;

use Board3r\ResponsiveImage\Commands\ThumbCommand;
use Board3r\ResponsiveImage\Support\ResponsiveImage;

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

