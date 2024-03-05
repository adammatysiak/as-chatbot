<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;
use App\Services\OpenAIService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CreateAssistant extends Command
{

    protected $signature = 'gpt:create_assistant';

    protected $description = 'Create GPT assistant in your OpenAI account.';

    protected $assistantName = 'WikiBot';

    protected $fileName;
    protected $fileId;

    protected $service;

    public function handle()
    {
        $this->service = new OpenAIService;

        $this->createAndUploadKnowledgeBase();

        $this->createOrUpdateAssistant();

        $this->line('Command finished.');
    }

    protected function createAndUploadKnowledgeBase()
    {
        $this->createFile();
        $this->line('File created.');

        $this->uploadFile();
        $this->line('File uploaded to OpenAI: '. $this->fileId);
    }

    protected function createFile()
    {
        $content = $this->consolidateFilesIntoMarkdown('wiki');

        $this->fileName = 'wiki-' . time() . '.md'; 

        Storage::disk('local')
            ->put($this->fileName, $content);
    }

    protected function consolidateFilesIntoMarkdown($directory) 
    {
        $allFilesContent = '';

        $files = Storage::disk('local')->files($directory, true);

        foreach ($files as $file) 
        {
            if (!Str::endsWith($file, ['.md', '.csv']))
                continue;

            $content = Storage::disk('local')->get($file);

            $fileName = basename($file);

            $allFilesContent .= "@@@ START OF $fileName\n\n$content\n\n@@@ END OF $fileName\n\n";
        }

        return $allFilesContent;
    }

    protected function uploadFile()
    {
        $uploadedFile = $this->service->getClient()
            ->files()
            ->upload([
                'file' => Storage::disk('local')->readStream($this->fileName),
                'purpose' => 'assistants',
            ]);

        $this->fileId = $uploadedFile->id;
    }

    protected function createOrUpdateAssistant()
    {
        if($this->isAlreadyExists()) {
            $this->info('Assistant "'. $this->assistantName .'" already exists. Updating...');

            $assistant = $this->updateAssistant();

            $this->info('Assistant updated. ID: '. $assistant->id);

            return;
        }

        $this->info('Assistant "'. $this->assistantName .'" is being created...');

        $assistant = $this->createAssistant();

        put_permanent_env('OPENAI_ASSISTANT_ID', $assistant->id);

        $this->info('Assistant created. ID: '. $assistant->id .' saved to env file.');
    }

    protected function isAlreadyExists()
    {
        return config('openai.assistant_id') && 
            $this->service->listAssistants()
                ->contains(function ($assistant) {
                    return strtolower($assistant['name'] ?? '') === strtolower($this->assistantName);
                });
    }

    protected function updateAssistant()
    {
        return $this->service->getClient()
            ->assistants()
            ->modify(
                config('openai.assistant_id'),
                $this->getAssistantData()
            );
    }

    protected function createAssistant()
    {
        return $this->service->getClient()
            ->assistants()
            ->create(
                $this->getAssistantData()
            );
    }

    protected function getAssistantData()
    {
        return [
            'name' => $this->assistantName,
            'description' => $this->description,
            'file_ids' => [
                $this->fileId
            ],
            'tools' => [
                [
                    'type' => 'retrieval',
                ],
            ],
            'instructions' => $this->getInstruction(),
            'model' => 'gpt-4-turbo-preview',
        ];   
    }

    protected function getInstruction()
    {
        return trans('chat.assistant');
    }
}