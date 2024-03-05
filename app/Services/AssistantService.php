<?php

namespace App\Services;

use App\Models\ChatThread;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;

class AssistantService implements OpenAICommunicator
{

    protected $openaiService;

    public function __construct()
    {
        $this->openaiService = new OpenAIService;
    }

    public function ask($content, $params = [])
    {
        $run = $this->sendMessage($content, data_get($params, 'conversation_uuid'));
        $result = $this->loadAnswer($run);
        return $this->getAnswer($result);
    }

    protected function sendMessage($content, $conversation_uuid)
    {
        if($this->isThreadExists($conversation_uuid))
            return $this->continueThread($content, $conversation_uuid);

        return $this->createThread($content, $conversation_uuid);
    }

    protected function isThreadExists($conversation_uuid)
    {
        return ChatThread::where('conversation_uuid', $conversation_uuid)->count() > 0;
    }

    protected function createThread($content, $conversation_uuid)
    {
        $run = $this->openaiService->getClient()->threads()->createAndRun([
            'assistant_id' => $this->getAssistantId(),
            'thread' => [
                'messages' => $this->openaiService->formatMessages($content),
            ],
        ]);

        ChatThread::create([
            'conversation_uuid' => $conversation_uuid,
            'thread_id' => $run->threadId
        ]);

        return $run;
    }

    protected function continueThread($content, $conversation_uuid)
    {
        $thread_id = ChatThread::where('conversation_uuid', $conversation_uuid)->first()->thread_id;

        $this->openaiService->getClient()->threads()->messages()->create(
            $thread_id, 
            $this->openaiService->formatMessages($content),
        );

        return $response = $this->openaiService->getClient()->threads()->runs()->create(
            threadId: $thread_id, 
            parameters: [
                'assistant_id' => $this->getAssistantId(),
            ],
        );
    }

    protected function loadAnswer(ThreadRunResponse $threadRun)
    {
        while(in_array($threadRun->status, ['queued', 'in_progress'])) {
            $threadRun = $this->openaiService->getClient()->threads()->runs()->retrieve(
                threadId: $threadRun->threadId,
                runId: $threadRun->id,
            );
        }

        if ($threadRun->status !== 'completed') {
            return null;
        }

        $messageList = $this->openaiService->getClient()->threads()->messages()->list(
            threadId: $threadRun->threadId,
        );

        return $messageList->data[0];
    }

    protected function getAnswer($result)
    {
        return data_get($result, 'content.0.text.value', null);
    }

    protected function getAssistantId()
    {
        return config('openai.assistant_id');
    }
}