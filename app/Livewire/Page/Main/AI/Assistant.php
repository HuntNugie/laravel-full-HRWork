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

    public array $messages = [];

    public bool $isLoading = false;

    public ?string $errorMessage = null;

    public function send(?string $submittedPrompt = null): void
    {
        $prompt = trim($submittedPrompt ?? $this->prompt);

        validator(
            ['prompt' => $prompt],
            ['prompt' => ['required', 'string', 'max:5000']]
        )->validate();

        if ($this->isLoading) {
            return;
        }

        if ($prompt === '') {
            return;
        }

        $this->errorMessage = null;
        $this->isLoading = true;

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
                if ($event instanceof TextDelta && $event->delta !== '') {
                    $this->stream(
                        content: $event->delta,
                        el: '#hrwork-ai-stream'
                    );
                }
            }

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response->text ?? '',
            ];
        } catch (Throwable $exception) {
            report($exception);

            array_pop($this->messages);

            $this->errorMessage = 'Terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.page.main.ai.assistant');
    }
}
