<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upload limits
    |--------------------------------------------------------------------------
    |
    | Values are configured in megabytes through the environment so they can
    | be changed per deployment without editing Filament resources. Filament's
    | maxSize() method receives the equivalent value in kilobytes below.
    |
    */

    'max_size_mb' => [
        'image' => (int) env('MEDIA_IMAGE_MAX_MB', 2),
        'audio' => (int) env('MEDIA_AUDIO_MAX_MB', 50),
        'video' => (int) env('MEDIA_VIDEO_MAX_MB', 100),
        'download' => (int) env('MEDIA_DOWNLOAD_MAX_MB', 50),
    ],

    'max_size_kb' => [
        'image' => (int) env('MEDIA_IMAGE_MAX_MB', 2) * 1024,
        'audio' => (int) env('MEDIA_AUDIO_MAX_MB', 50) * 1024,
        'video' => (int) env('MEDIA_VIDEO_MAX_MB', 100) * 1024,
        'download' => (int) env('MEDIA_DOWNLOAD_MAX_MB', 50) * 1024,
    ],

    // Uploaded files use Laravel's public local disk for the first release.
    'disk' => env('MEDIA_DISK', 'public'),

    // External media URLs (for example, Aparat or YouTube) are allowed.
    'allow_external_urls' => (bool) env('MEDIA_ALLOW_EXTERNAL_URLS', true),

];
