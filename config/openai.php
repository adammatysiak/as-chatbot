<?php

return [
    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),
    'model' => env('OPENAI_MODEL', 'gpt-4-turbo-preview'),

    // required for Azure OpenAI
    'api_version' => env('OPENAI_API_VERSION'),
    'deployment' => env('OPENAI_DEPLOYMENT'),

    // needed for Assistant-support
    'assistant_id' => env('OPENAI_ASSISTANT_ID'),
];