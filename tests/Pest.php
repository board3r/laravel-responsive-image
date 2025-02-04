<?php

use Board3r\ResponsiveImage\View\Components\ResponsiveImage;

uses(
    Tests\TestCase::class,
)->in(__DIR__);

/**
 * Helper to assert test on component render
 */
function expectComponent(ResponsiveImage $component): void
{
    $view = $component->render();
    $data = $component->data();
    if (env('TEST_DUMP_COMPONENT')) {
        dump($view->render());
    }
    expect($view->render())->toBeString()
        ->and($data['image'])->toBeString()
        ->and($data['width'])->toBeNumeric()
        ->and($data['height'])->toBeNumeric();
}
