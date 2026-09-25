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

    public string $question = '';

    public string $answer = '';

    public array $messages = [];

    public bool $isAsking = false;

    public ?string $errorMessage = null;

    /**
     * Start a new assistant turn.
     *
     * This follows Livewire's recommended chat streaming flow:
     * submit the prompt first, then trigger the streamed ask() call.
     */
    public function submitPrompt(): void
    {
        $prompt = trim($this->prompt);

        validator(
            ['prompt' => $prompt],
            ['prompt' => ['required', 'string', 'max:5000']]
        )->validate();

        if ($this->isAsking || $prompt === '') {
            return;
        }

        $this->errorMessage = null;
        $this->question = $prompt;
        $this->prompt = '';
        $this->answer = '';
        $this->isAsking = true;

        $this->messages[] = [
            'role' => 'user',
            'content' => $prompt,
        ];

        // Start the second request after the user turn has been rendered.
        // This keeps the stream target in the DOM before the first token arrives.
        $this->js('$wire.ask()');
    }

    /**
     * Stream the assistant response into the active WireKit assistant message.
     */
    public function ask(): void
    {
        if (! $this->isAsking || trim($this->question) === '') {
            return;
        }

        try {
            $response = HRAssistant::make()->stream(
                $this->question,
                provider: '9router',
                model: config('ai.providers.9router.models.text.default')
            );

            foreach ($response as $event) {
                if (! $event instanceof TextDelta || $event->delta === '') {
                    continue;
                }

                $this->stream(
                    content: $event->delta,
                    el: '#hrwork-ai-stream-answer',
                    replace: false,
                );
            }

            $this->answer = $response->text ?? $this->answer;

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $this->answer,
            ];
        } catch (Throwable $exception) {
            report($exception);

            if (($this->messages[array_key_last($this->messages)]['role'] ?? null) === 'user') {
                array_pop($this->messages);
            }

            $this->answer = '';
            $this->errorMessage = 'Terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';
        } finally {
            $this->isAsking = false;
        }
    }

    public function render()
    {
        return view('livewire.page.main.ai.assistant');
    }
}
