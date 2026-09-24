<?php

return [

    'default' => '9router',

    'default_for_images' => '9router',
    'default_for_audio' => '9router',
    'default_for_transcription' => '9router',
    'default_for_embeddings' => '9router',
    'default_for_reranking' => '9router',
    'default_for_classification' => '9router',

    'conversations' => [
        'connection' => env('AI_CONVERSATIONS_CONNECTION'),
        'tables' => [
            'conversations' => env('AI_CONVERSATIONS_TABLE', 'agent_conversations'),
            'messages' => env('AI_CONVERSATION_MESSAGES_TABLE', 'agent_conversation_messages'),
        ],
    ],

    'providers' => [

        '9router' => [
            'driver' => 'openai-compatible',
            'url' => env('OPENAI_COMPATIBLE_URL'),
            'key' => env('OPENAI_COMPATIBLE_API_KEY'),
            'models' => [
                'text' => [
                    'default' => env('OPENAI_COMPATIBLE_MODEL', 'op-key/gemini-3.8-flash'),
                ],
            ],
        ],

    ],

];
