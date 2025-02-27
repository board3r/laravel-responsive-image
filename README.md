# Laravel Responsive Image

Blade component and url routing to generate responsive images and thumbnails.

Be lazy, just use an image and the package will be responsive alone.

Boost the PageSpeed.

## Requirements

* PHP >= 8.2
* Laravel 11+

### Image processing

This package use by default [intervention/image](https://image.intervention.io/v3), needed to have at least
one [image processing extension](https://image.intervention.io/v3/introduction/installation) installed.

### Image optimizer

This package use by default [spatie/image-optimizer](https://github.com/spatie/image-optimizer), see [optimization tools section](https://github.com/spatie/image-optimizer)

## Install

```
composer require board3r/laravel-responsive-image
```

## Basic usage

Store the images in the laravel local disk in the directory /storage/app/private/responsive-image (default settings)

```
<x-board3r::responsive-image
    image="mountain.png"
    alt="Beautifull mountain"
    :thumbs="{{['w'=>800,'w'=>600]}}"
    class="rounded border-white border-5"
    sizes="(min-width: 990px) calc(100vw / 3),
     (min-width: 480px) calc(100vw / 2),
     calc((100vw - 3rem) / 2)"
/>
```

Html render :

```
<img src="/image-responsive/mountain.png"
    alt="Beautifull mountain"
    srcset="/img/mountain.png?w=800 w800
            /img/mountain.png?w=600 w600"
    sizes="(min-width: 990px) calc(100vw / 3),
     (min-width: 480px) calc(100vw / 2),
     calc((100vw - 3rem) / 2)"            
    width="1600" 
    height="900"
    loading="lazy"          
/>
```

All thumbnails are ready using the package routes.

## Documentation

### Configuration
The configuration will be published automatically under the folder _/config/responsive-image.php_ during the installation.

In the documentation the tag @conf make a reference of the config file of this package.

All the example are illustrated with default Laravel And Laravel Responsive Image configuration and environnement.

### Component parameters
#### image (required)
Set the src attribute image of img html tag.
```
// The origin image must be stored in /storage/app/private/responsive-image/mountain.jpg" 
<x-board3r::responsive-image
    ...
    image="mountain.jpg"
    ...
/>
```
```
// Possibilty to use subfolders, the origin image must be stored in /storage/app/private/responsive-image/subdir/mountain.jpg" 
<x-board3r::responsive-image
    ...
    image="/subdir/mountain.jpg"
    ...
/>
```
```
// Possibilty to external image, the origin image will be stored automatically in /storage/app/private/responsive-image/picsum.photos/450-1600x900.jpg" 
<x-board3r::responsive-image
    ...
    image="https://picsum.photos/id/450/1600/900"
    ...
/>
```
@conf
* _storage.origin_ : The original image storage (default: 'local') 
* _img_path_ : The directory in the storage disk (default: '/responsive-image')
* _allowed_extension_ : Allowed extension of original image (default:['webp', 'jpg', 'jpeg', 'png', 'gif'] )

#### :thumbs (optional)
List the thumbnails needed for good responsive performance.
Pass an array for each thumb with one or more following query parameters 
* _w_ : An integer to define the width
* _h_ : An integer to define height, if the height and width are defined the image will be cropped automatically
* _c_ : Define a position to crop (default: 'center')
* _f_ : Format of the thumbnail (default: 'webp')

``` 
// Generate two thumbs, one with 400px width and an other with 600px width
<x-board3r::responsive-image
    ...
    :thumbs="[['w'=>400],['w'=>600]]"
    ...
/>
```
``` 
// Cropped thumbnails
<x-board3r::responsive-image
    ...
    :thumbs="[['w'=>400,'h'=>400] ,['w'=>100,'h'=>100,'c'=>'top']]"
    ...
/>
```
``` 
// Output in a different format
<x-board3r::responsive-image
    ...
    :thumbs="[['w'=>400,'f'=>'jpg'],['w'=>600,'f'=>'jpg']]"
    ...
/>
```
@conf

* _default_srcset_ : If :thumbs parameter is not set, a default set will be used (default: [['w' => 100], ['w' => 200], ['w' => 400], ['w' => 800], ['w' => 1200]])
* _img_thumb_path_ : The directory in the storage disk (default: '/responsive-image/thumbs')
* _default_thumb_ext_ : The default format to generate thumbnail (default :'webp'). The default value must be allowed in _allowed_format_
* _allowed_format_ : Allowed format to generate thumbnail (default:  ['webp', 'jpg', 'png', 'gif'])
* _allowed_width_ : Allowed widths to generate thumbnail (default:  [50, 100, 200, 400, 600, 800, 1200, 1400, 1600]). Possibility to allow all widths with an empty array
* _allowed_height_ : Allowed heights to generate thumbnail (default:  [50, 100, 200, 300, 400, 500, 600, 800, 1000, 1200]). Possibility to allow all heights with an empty array
* _allowed_crop_ : Allowed crop positions to generate thumbnail (default: ['center', 'top', 'top-right', 'top-left', 'left', 'bottom', 'bottom-right', 'bottom-left', 'right'])
* _storage.thumb_ : The thumbnail storage (default: 'public')
* _thumb_dir_level_ : Thumbnail will generate a tree storage. This parameter define the number of level (default: 2)

#### :useCustomThumbs
By default this parameter is disabled, but the package offer the possibility to use specifics thumbnails and this parameter is set to true.

Don't forget to specify a width associated with the thumbnail to stay responsive.
``` 
<x-board3r::responsive-image
    ...
    :thumbs="['https://picsum.photos/id/450/800/600 600w','https://picsum.photos/id/450/400/200 200w']"
    :useCustomThumbs="true"
    ...
/>
``` 

#### loading, fetchPriority and decoding
These parameters will impact the page loading.

Allowed values :
* _loading (default: 'lazy')_ : 'auto','lazy','eager' [more information](https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/loading)
* _fetchPriority_ : 'high','low','auto' [more information](https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/fetchPriority)
* _decoding_ : 'sync','async','lazy' [more information](https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/decoding)

#### width and height (optional)
By default these values are calculated, but force them to avoid some problems of calculation
```
<x-board3r::responsive-image
    ...
    width="1600"
    height="900"
    ...
/>
```
### Get more attributes
As all components in Laravel, you can add other attributes, they will reuse to display the image.
```
<x-board3r::responsive-image
    ...
    alt="Beautifull moutain"
    class="responsive-image"
    ...
/>
```
Don't forget important thing to keep the image responsive and a performant page loading, the attribute _sizes_.

This attribute is not directly manage by the package because each case is different, but it's very important to set it. [More information](https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/sizes) 
```
// an example of attributes size to determine which thumnail to use... 
<x-board3r::responsive-image
    ...
    sizes="(min-width: 1600px) 367px, 
    (min-width: 990px) calc((100vw - 10rem) / 4),
    (min-width: 750px) calc((100vw - 10rem) / 3),
    (min-width: 480px) calc((100vw - 5rem) / 2),
    calc((100vw - 3rem) / 2)"
    ...
/>
```
## Environnement

Possibility to use some environnement variable to configure a default config value
```
RESPONSIVE_IMAGE_CACHE_TIME=2592000
RESPONSIVE_IMAGE_URL_PATH=/img
RESPONSIVE_IMAGE_IMG_PATH=/responsive-image
RESPONSIVE_IMAGE_IMG_THUMB_PATH=/responsive-image/thumbs
RESPONSIVE_IMAGE_DEFAULT_THUMB_EXT=webp
RESPONSIVE_IMAGE_THUMB_DIR_LEVEL=2
RESPONSIVE_IMAGE_STORAGE_ORIGIN=local
RESPONSIVE_IMAGE_STORAGE_THUMB=public
```

## Extending Processors
The package is only delivered with [Intervention Image](https://image.intervention.io/v3)

Possibility to extend with another image processor following the _ProcessorInterface::class_

@conf
* _processor.class_ (default: InterventionProcessor::class)_ : Define the image processor class

## Extending Optimizers
The package is delivered with [Spatie Image Optimizer](https://github.com/spatie/image-optimizer) and another one to disable image optimisation (_DisableOptimizer::class_) 

Possibility to extend with another image optimizer following the _OptimizerInterface::class_

@conf
* _optimizer.class_ (default: SpatieOptimizer::class)_ : Define the image optimisation class
