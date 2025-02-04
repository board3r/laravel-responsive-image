# Laravel Responsive Image

Blade component and url routing to generate responsive images and thumbnails.

Be lazy, just use an image and the package will be responsive alone.

Boost your PageSpeed.

## Install

```
composer require board3r/laravel-responsive-image
```

## Requirements

* PHP >= 8.2
* Laravel 11

### Image processing

This package use by default [intervention/image](https://image.intervention.io/v3), you need to have at least
one [image processing extension](https://image.intervention.io/v3/introduction/installation) installed.

### Image optimizer

This package use by default [spatie/image-optimizer](https://github.com/spatie/image-optimizer), see [optimization tools section](https://github.com/spatie/image-optimizer)

## Basic usage

Store your images in the laravel local disk in the directory /storage/app/private/responsive-image (default settings)

```
<x-board3r::responsive-image
    image="mountain.png"
    alt="Beautifull mountain"
    :thumbs="{{['w'=>800,'w'=>600]}}"
    class="rounded border-white border-5"
    sizes="(min-width: 990px) calc(100vw / 3),
     (min-width: 750px) calc(100vw / 2),
     (min-width: 480px) calc(100vw / 2),
     calc((100vw - 3rem) / 2)"
/>
```

Html render :

```
<img src="/image-responsive/mountain.png"
    srcset="/img/mountain.png?w=800 w800
            /img/mountain.png?w=600 w600"
    width="1600" 
    height="900"
    loading="lazy"        
    alt="Beautifull mountain"
/>
```

All thumbnails are ready using the package routes.

## Documentation

Coming soon

