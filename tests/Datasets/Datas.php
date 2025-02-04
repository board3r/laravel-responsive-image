<?php

dataset('format available', ['webp', 'jpg', 'png', 'gif']);

dataset('thumbs', [
    'Basic' => [[['w' => 400], ['w' => 200]]],
    'One width not allowed' => [[['w' => 400], ['w' => 200], ['w' => 300]]],
    'Height only' => [[['h' => 400], ['h' => 200]]],
    'Crop' => [[['w' => 400, 'h' => 200], ['w' => 200, 'h' => 100]]],
    'Crop with position' => [[['w' => 400, 'h' => 200, 'c' => 'bottom'], ['w' => 200, 'h' => 100, 'c' => 'right']]],
    'All format' => [[['w' => 400, 'f' => 'gif'], ['w' => 200, 'f' => 'png'], ['w' => 600, 'f' => 'png'], ['w' => 800, 'f' => 'webp']]],
]);

dataset('width', [null, 200, 800]);
dataset('height', [null, 400]);
dataset('crop', [null, 'top', 'bottom-right']);
dataset('ext', [null, 'webp', 'jpg', 'png', 'gif']);
