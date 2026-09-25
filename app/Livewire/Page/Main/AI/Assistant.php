<?php

namespace App\Livewire\Page\Main\AI;

use App\Ai\Agents\HRAssistant;
use Laravel\Ai\Messages\Message;
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

        // Keep the current request out of the history we send to the model.
        // The current prompt is passed separately to stream(), so it appears
        // exactly once in the model context.
        $history = collect($this->messages)
            ->map(
                fn (array $message) => new Message(
                    $message['role'],
                    $message['content'],
                )
            )
            ->all();

        $this->messages[] = [
            'role' => 'user',
            'content' => $prompt,
        ];
        $this->prompt = '';

        try {
            $response = HRAssistant::make()
                ->withMessages($history)
                ->stream(
                    $prompt,
                    provider: '9router',
                    model: config('ai.providers.9router.models.text.default')
                );

            // Drain the SSE stream completely so Laravel AI can execute
            // tool calls and continue the agent loop until the final answer.
            foreach ($response as $event) {
                // The UI renders the completed answer as one message.
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
