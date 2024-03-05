<?php

namespace App\Services;

class ChatService implements OpenAICommunicator
{

    protected $openaiService;

    public function __construct()
    {
        $this->openaiService = new OpenAIService;
    }

    public function ask($content, $params = [])
    {
        $client = $this->openaiService->getClient();

        return $client->chat()->create([
            'model' => config('openai.model'),
            'messages' => $this->openaiService->formatMessages($content),
            'max_tokens' => data_get($params, 'max_tokens', 500),
        ]);
    }
}