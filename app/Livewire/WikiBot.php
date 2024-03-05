<?php

namespace App\Livewire;

use App\Services\AssistantService;
use Livewire\Component;

class WikiBot extends Component
{

    public string $question = '';

    public ?string $answer = null;

    public function render()
    {
        return view('livewire.wiki-bot');
    }

    ///////

    public function ask()
    {
        $service = new AssistantService;

        $this->answer = $service->ask($this->question, [
            'conversation_uuid' => time(),
        ]);
    }

}
