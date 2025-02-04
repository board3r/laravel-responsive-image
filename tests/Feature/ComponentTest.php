<?php

namespace Tests\Feature;

use Board3r\ResponsiveImage\Support\ResponsiveImage;
use Board3r\ResponsiveImage\View\Components\ResponsiveImage as Component;

test('Simple component', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => [['w' => 200, 'h' => 200]],
    ]);
    $view->assertSee('src="'.ResponsiveImage::originPath('mountain.jpg').'"', false)
        ->assertSee('srcset="'.ResponsiveImage::urlThumbPath('mountain.jpg').'?w=200&amp;h=200 200w"', false)
        // height and width calculated
        ->assertSee('width="1600"', false)
        ->assertSee('height="1024"', false)
        // lazy loading by default
        ->assertSee('loading="lazy"', false);
});

test('Multiple thumbs', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => [['w' => 400], ['w' => 800]],
    ]);
    // thumbs are always order by desc width size
    $view->assertSeeInOrder([
        ResponsiveImage::urlThumbPath('mountain.jpg').'?w=800 800w',
        ResponsiveImage::urlThumbPath('mountain.jpg').'?w=400 400w'], false);
});

test('Dataset thumbs', function ($thumbs) {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => $thumbs,
    ]);
    $view->assertSee('src="'.ResponsiveImage::originPath('mountain.jpg').'"', false);
})->with('thumbs');

test('Default thumbs', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
    ]);
    // if no thumbs are set, use the default set in the config , default thumbs are width 800,400,200,100
    $view->assertSeeInOrder([
        ResponsiveImage::urlThumbPath('mountain.jpg').'?w=800 800w',
        ResponsiveImage::urlThumbPath('mountain.jpg').'?w=400 400w',
        ResponsiveImage::urlThumbPath('mountain.jpg').'?w=200 200w',
        ResponsiveImage::urlThumbPath('mountain.jpg').'?w=100 100w'], false);
});

test('Thumb with all option', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => [
            ['w' => 400, 'h' => 400, 'c' => 'bottom', 'f' => 'png'],
        ],
    ]);
    // the order of query params are the same
    $view->assertSee('srcset="'.ResponsiveImage::urlThumbPath('mountain.jpg').'?w=400&amp;h=400&amp;c=bottom&amp;f=png 400w"', false);
});

test('Thumb with only height', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => [
            ['h' => 400],
        ],
    ]);
    // When only height is specified, the width is calculated proportionally to origin image (in this case 400x1600/1024 = 625)
    $view->assertSee('srcset="'.ResponsiveImage::urlThumbPath('mountain.jpg').'?h=400 625w"', false);
});

test('Not allowed width', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => [
            ['w' => 400],
            ['w' => 500],
        ],
    ]);
    // 500 width is not allowed by default (@see config) so thumbnail path is not generated
    $view->assertDontSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=500 500w', false)
        ->assertSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=400 400w', false);
});

test('Not allowed height', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => [
            ['h' => 400],
            ['h' => 700],
        ],
    ]);
    // 900 height is not allowed by default (@see config) so thumbnail path is not generated
    $view->assertDontSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?h=700 1094w', false)
        ->assertSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?h=400 625w', false);
});

test('Not allowed crop position', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => [
            ['w' => 400, 'h' => 400, 'c' => 'bottom'],
            ['w' => 200, 'h' => 200, 'c' => 'aside'],
            ['w' => 600, 'c' => 'bottom'],
        ],
    ]);
    // Crop position 'aside' is not allowed by default (@see config) so thumbnail path is not generated
    $view->assertDontSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=200&amp;h=200&amp;c=aside 200w', false)
        // crop position need to have width and height specified
        ->assertDontSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=600&amp;c=bottom 400w', false)
        ->assertSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=400&amp;h=400&amp;c=bottom 400w', false);
});

test('Not allowed thumb format', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => [
            ['w' => 400, 'f' => 'jpg'],
            ['w' => 600, 'f' => 'jpeg'],
            ['w' => 800, 'f' => config('responsive-image.default_thumb_ext', 'webp')],
        ],
    ]);
    // Format 'jpeg' is not allowed , set instead jpg. It is possible to limit allowed format (@see config)
    $view->assertDontSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=600&amp;f=jpeg 600w', false)
        ->assertSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=400&amp;f=jpg 400w', false)
        // if the format passed if the default one, he is automatically removed
        ->assertSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=800 800w', false)
        ->assertDontSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=800&amp;f=webp 800w', false);
});

test('Width must me unique', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => [
            ['w' => 400, 'h' => 400],
            ['w' => 400, 'h' => 200],
        ],
    ]);
    // To use srcset and responsive image the width must be unique, it keeps only the first in thumbs
    $view->assertDontSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=400&amp;h=200 400w', false)
        ->assertSee(ResponsiveImage::urlThumbPath('mountain.jpg').'?w=400&amp;h=400 400w', false);
});

test('Subfolder origin', function () {
    $view = $this->component(Component::class, [
        'image' => 'subfolder/mountain.jpg',
        'thumbs' => [['w' => 400]],
    ]);
    // the origin subfolder must be used for thumbnail
    $view->assertSee('src="'.ResponsiveImage::originPath('subfolder/mountain.jpg').'"', false)
        ->assertSee('srcset="'.ResponsiveImage::urlThumbPath('subfolder/mountain.jpg').'?w=400 400w"', false);
});

test('Force size of original picture', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => [['w' => 400]],
        'width' => 1200,
        'height' => 800,
    ]);
    // the size can be false when force it
    $view->assertSee('width="1200"', false)
        ->assertSee('height="800"', false);
});

test('External image', function () {
    $view = $this->component(Component::class, [
        'image' => 'https://picsum.photos/id/450/1600/900',
        'thumbs' => [['w' => 400]],
    ]);
    // image are stored under a subfolder with the hostname
    $view->assertSee('src="'.ResponsiveImage::originPath('picsum.photos/450-1600x900.jpg').'"', false)
        ->assertSee(ResponsiveImage::urlThumbPath('picsum.photos/450-1600x900.jpg?w=400 400w'), false)
        // size of external image must be calculated
        ->assertSee('width="1600"', false)
        ->assertSee('height="900"', false);
});

test('Loading parameter', function () {
    // only accept eager and auto
    $values = ['eager' => 'See', 'auto' => 'See', 'wrong' => 'DontSee'];
    foreach ($values as $value => $assert) {
        $view = $this->component(Component::class, [
            'image' => 'mountain.jpg',
            'thumbs' => [['w' => 400]],
            'loading' => $value,
        ]);
        $view->{'assert'.$assert}('loading="'.$value.'"', false);
    }
});

test('FetchPriority parameter', function () {
    // only accept eager and auto
    $values = ['high' => 'See', 'low' => 'See', 'auto' => 'See', 'wrong' => 'DontSee'];
    foreach ($values as $value => $assert) {
        $view = $this->component(Component::class, [
            'image' => 'mountain.jpg',
            'thumbs' => [['w' => 400]],
            'fetchPriority' => $value,
        ]);
        $view->{'assert'.$assert}('fetchPriority="'.$value.'"', false);
    }
});

test('Decoding parameter', function () {
    // only accept eager and auto
    $values = ['sync' => 'See', 'async' => 'See', 'lazy' => 'See', 'wrong' => 'DontSee'];
    foreach ($values as $value => $assert) {
        $view = $this->component(Component::class, [
            'image' => 'mountain.jpg',
            'thumbs' => [['w' => 400]],
            'decoding' => $value,
        ]);
        $view->{'assert'.$assert}('decoding="'.$value.'"', false);
    }
});

test('Use custom thumbs', function () {
    $view = $this->component(Component::class, [
        'image' => 'mountain.jpg',
        'thumbs' => ['https://picsum.photos/id/450/400 400w', 'https://picsum.photos/id/450/600 600w'],
        'useCustomThumbs' => true,
    ]);
    // Just use specific thumb without process them
    $view->assertSee('https://picsum.photos/id/450/600 600w', false)
        ->assertSee('https://picsum.photos/id/450/400 400w', false);
});

test('Origin image no exist', function () {
    $view = $this->component(Component::class, [
        'image' => 'fake.jpg',
        'thumbs' => [['w' => 400]],
    ]);
    // if the image doesn't exist just
    $view->assertSee('src="'.ResponsiveImage::originPath('fake.jpg').'"', false)
        ->assertDontSee('srcset="', false);
});
// @todo if thumb size bigger than original, no render it
