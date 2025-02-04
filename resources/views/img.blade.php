<x-board3r::responsive-image
    class="responsive-image"
    sizes="(min-width: 990px) calc((100vw - 10rem) / 4), (min-width: 750px) calc((100vw - 10rem) / 3),(min-width: 480px) calc((100vw - 5rem) / 2), calc((100vw - 3rem) / 2)"
    image="mountain.jpg"
    :thumbs="[['w'=>800,'h'=>600,'f'=>'jpg','c'=>'bottom'],['w'=>400],['w'=>200]]"
    {{--
    //Possibility to set the size of origin image manually, but if the is omitted, it will be generated automatically
    width="1600"
    height="900"
    // by default the attribute loading will be set to "lazy", if needed it can be set to "eager"
    loading="lazy"
    --}}
    fetchPriority="{{$fetchPriority}}"
    decoding="{{$decoding}}"
    :useCustomThumbs="false"
/>
