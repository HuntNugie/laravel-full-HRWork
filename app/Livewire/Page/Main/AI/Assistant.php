<?php

namespace App\Livewire\Page\Main\AI;

use App\Ai\Agents\HRAssistant;
use Laravel\Ai\Messages\Message;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

#[Layout('layouts.main', ['title' => 'HRWork AI'])]
class Assistant extends Component
{
    public string $prompt = '';

    #[Locked]
    public array $messages = [];

    #[Locked]
    public string $accessFingerprint = '';

    #[Locked]
    public bool $isLoading = false;

    public ?string $errorMessage = null;

    private function authorizeAssistant(): void
    {
        $user = auth()->user();
        abort_unless($user && $user->status === 'active' && $user->hasRole('hr') && ! $user->hasRole('administrator') && $user->can('view-ai-assistant'), 403);
        $fingerprint = hash('sha256', $user->id.':'.$user->getAllPermissions()->pluck('name')->sort()->implode('|'));
        if ($this->accessFingerprint !== $fingerprint) {
            $this->messages = [];
            $this->accessFingerprint = $fingerprint;
        }
    }

    public function clearConversation(): void
    {
        $this->authorizeAssistant();
        $this->reset(['messages', 'prompt', 'errorMessage']);
    }

    public function send(?string $submittedPrompt = null): void
    {
        $this->authorizeAssistant();
        if (! config('ai.hrwork_enabled') || blank(config('ai.providers.9router.url')) || blank(config('ai.providers.9router.key')) || blank(config('ai.providers.9router.models.text.default'))) {
            $this->errorMessage = 'AI belum diaktifkan atau konfigurasi provider belum lengkap. Hubungi Administrator.';
            return;
        }
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
        $this->messages = array_slice($this->messages, -12);
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
            // Log the source location, not raw provider payloads or employee data.
            logger()->warning('HRWork AI request failed', [
                'exception_type' => get_class($exception),
                'file' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $exception->getFile()),
                'line' => $exception->getLine(),
                'diagnostic' => $exception instanceof \ErrorException
                    ? match (true) {
                        str_starts_with($exception->getMessage(), 'Undefined array key') => 'undefined_array_key',
                        str_starts_with($exception->getMessage(), 'Trying to access array offset') => 'invalid_array_offset',
                        str_starts_with($exception->getMessage(), 'Undefined property') => 'undefined_property',
                        default => 'php_error',
                    }
                    : 'request_failed',
            ]);

            array_pop($this->messages);

            $this->errorMessage = 'Terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        $this->authorizeAssistant();
        return view('livewire.page.main.ai.assistant');
    }
}
