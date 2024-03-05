<?php

namespace App\Services;

use OpenAI\Factory as OpenAIFactory;

class OpenAIService
{

    public function listAssistants()
    {
        $response = $this->getClient()->assistants()->list();
        return collect($response->data);
    }

    public function getClient()
    {
        if(config('openai.deployment') != null)
            return $this->getAzureClient();

        return $this->getOpenAIClient();
    }

    protected function getAzureClient()
    {
        return (new OpenAIFactory)
            ->withBaseUri(config('openai.organization'). '.openai.azure.com/openai/')
            ->withHttpHeader('api-key', config('openai.api_key'))
            ->withHttpHeader('OpenAI-Beta', 'assistants=v1')
            ->withQueryParam('api-version', config('openai.api_version'))
            ->make();
    }

    protected function getOpenAIClient()
    {
        return (new OpenAIFactory)
            ->withApiKey(config('openai.api_key'))
            ->withHttpHeader('api-key', config('openai.api_key'))
            ->withHttpHeader('OpenAI-Beta', 'assistants=v1')
            ->withOrganization(config('openai.organization'))
            ->make();
    }

    public function formatMessages($content)
    {
        if (is_string($content)) {
            $content = [
                ['user', $content]
            ];
        }

        return collect($content)
            ->map(function ($message) {
                return [
                    'role' => $message[0],
                    'content' => $message[1],
                ];
            })
            ->all();
    }

}