@props(['loading','fetchPriority','decoding'])

<img src="{{$src}}"
     @if($srcset) srcset="{{$srcset}}" @endif
     {{ $attributes }}
     @if($width) width="{{$width}}" @endif
     @if($height) height="{{$height}}" @endif
     @if($loading) loading="{{$loading}}" @endif
     @if($fetchPriority) fetchPriority="{{$fetchPriority}}" @endif
     @if($decoding) decoding="{{$decoding}}" @endif
/>
