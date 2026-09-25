<?php

namespace App\Livewire\Page\Main\AI;

use App\Ai\Agents\HRAssistant;
use Laravel\Ai\Streaming\Events\TextDelta;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.main', ['title' => 'HRWork AI'])]
class Assistant extends Component
{
    public string $prompt = '';

    public string $answer = '';

    public array $messages = [];

    public ?string $errorMessage = null;

    public function send(?string $submittedPrompt = null): void
    {
        $prompt = trim($submittedPrompt ?? $this->prompt);

        validator(
            ['prompt' => $prompt],
            ['prompt' => ['required', 'string', 'max:5000']]
        )->validate();

        if ($prompt === '') {
            return;
        }

        $this->errorMessage = null;
        $this->answer = '';

        $this->messages[] = [
            'role' => 'user',
            'content' => $prompt,
        ];

        $this->prompt = '';

        try {
            $response = HRAssistant::make()->stream(
                $prompt,
                provider: '9router',
                model: config('ai.providers.9router.models.text.default')
            );

            foreach ($response as $event) {
                if (! $event instanceof TextDelta || $event->delta === '') {
                    continue;
                }

                $this->answer .= $event->delta;

                $this->stream(
                    content: $event->delta,
                    el: '#hrwork-ai-stream-answer',
                    replace: false,
                );
            }

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response->text ?? $this->answer,
            ];

            $this->answer = '';
        } catch (Throwable $exception) {
            report($exception);

            if (($this->messages[array_key_last($this->messages)]['role'] ?? null) === 'user') {
                array_pop($this->messages);
            }

            $this->answer = '';
            $this->errorMessage = 'Terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';
        }
    }

    public function render()
    {
        return view('livewire.page.main.ai.assistant');
    }
}
