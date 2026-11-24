<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | HRWork currently routes text generation through the internal
    | OpenAI-compatible gateway (9Router).
    |
    */

    'default' => '9router',
    'hrwork_enabled' => env('HRWORK_AI_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | AI Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        '9router' => [
            'driver' => 'openai-compatible',
            'url' => env('OPENAI_COMPATIBLE_URL'),
            'key' => env('OPENAI_COMPATIBLE_API_KEY'),
            'models' => [
                'text' => [
                    'default' => env(
                        'OPENAI_COMPATIBLE_MODEL',
                        ''
                    ),
                ],
            ],
        ],
    ],

];
