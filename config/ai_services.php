<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Video Generation Services
    |--------------------------------------------------------------------------
    |
    | Configuration for AI video generation services.
    | You can use free tiers of various providers.
    |
    */

    'video_services' => [
        'default' => 'runwayml', // Default service to use
        
        'runwayml' => [
            'name' => 'RunwayML',
            'api_key' => env('RUNWAYML_API_KEY'),
            'api_url' => 'https://api.runwayml.com/v1',
            'free_tier' => true,
            'max_duration' => 30, // seconds
            'supported_formats' => ['mp4'],
        ],
        
        'pika' => [
            'name' => 'Pika Labs',
            'api_key' => env('PIKA_API_KEY'),
            'api_url' => 'https://api.pika.art/v1',
            'free_tier' => true,
            'max_duration' => 15,
            'supported_formats' => ['mp4', 'gif'],
        ],
        
        'stable_video' => [
            'name' => 'Stable Video Diffusion',
            'api_key' => env('STABLE_VIDEO_API_KEY'),
            'api_url' => 'https://api.stability.ai/v1/video',
            'free_tier' => true,
            'max_duration' => 20,
            'supported_formats' => ['mp4'],
        ],
        
        'kaiber' => [
            'name' => 'Kaiber',
            'api_key' => env('KAIBER_API_KEY'),
            'api_url' => 'https://api.kaiber.ai/v1',
            'free_tier' => true,
            'max_duration' => 30,
            'supported_formats' => ['mp4'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Image Enhancement Services
    |--------------------------------------------------------------------------
    */
    'image_services' => [
        'default' => 'replicate',
        
        'replicate' => [
            'name' => 'Replicate',
            'api_key' => env('REPLICATE_API_KEY'),
            'api_url' => 'https://api.replicate.com/v1',
            'free_tier' => true,
            'models' => [
                'enhance' => 'stability-ai/sdxl-enhance',
                'upscale' => 'stability-ai/sdxl-upscale',
                'advertisement' => 'stability-ai/stable-diffusion',
            ],
        ],
        
        'fal' => [
            'name' => 'FAL',
            'api_key' => env('FAL_API_KEY'),
            'api_url' => 'https://api.fal.ai/v1',
            'free_tier' => true,
            'models' => [
                'enhance' => 'imageutils/real-esrgan',
                'upscale' => 'imageutils/real-esrgan-x4',
                'advertisement' => 'stability-ai/stable-diffusion',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Generation Settings
    |--------------------------------------------------------------------------
    */
    'settings' => [
        'default_video_duration' => 15, // Default video duration in seconds
        'min_video_duration' => 10,    // Minimum video duration
        'max_video_duration' => 30,    // Maximum video duration
        'video_quality' => 'high',        // low, medium, high
        'aspect_ratio' => '9:16',        // For vertical videos
        'enable_fallback' => true,        // Use FFmpeg fallback if AI services fail
        'ffmpeg_path' => env('FFMPEG_PATH', 'ffmpeg'),
    ],
];
