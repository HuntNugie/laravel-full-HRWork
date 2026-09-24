<?php

namespace App\Livewire\Page\Main\AI;

use App\Ai\Agents\HRAssistant;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Laravel\Ai\Responses\Data\ToolCall as ToolCallData;
use Laravel\Ai\Streaming\Events\ToolCall as ToolCallEvent;
use Laravel\Ai\Streaming\Events\ToolResult as ToolResultEvent;
use Throwable;

#[Layout('layouts.main', ['title' => 'HRWork AI'])]
class Assistant extends Component
{
    public string $prompt = '';

    /**
     * @var list<array{
     *     role: string,
     *     content: string,
     *     tool_calls?: list<array{
     *         id: string,
     *         name: string,
     *         status: string,
     *         arguments: array<string, mixed>|string,
     *         seconds?: int|null,
     *         result?: string
     *     }>
     * }>
     */
    public array $messages = [];

    public bool $isLoading = false;

    public ?string $errorMessage = null;

    /**
     * @var array<string, array{
     *     id: string,
     *     name: string,
     *     arguments: array<string, mixed>,
     *     status: string,
     *     started_at: int,
     *     result?: string,
     *     seconds?: int|null
     * }>
     */
    public array $toolCalls = [];

    public function send(): void
    {
        $this->validate([
            'prompt' => ['required', 'string', 'max:5000'],
        ]);

        if ($this->isLoading) {
            return;
        }

        $prompt = trim($this->prompt);

        if ($prompt === '') {
            return;
        }

        $this->errorMessage = null;
        $this->isLoading = true;
        $this->toolCalls = [];

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

            // Drain the SSE stream completely so Laravel AI can execute local
            // tools and continue the agent loop until the final answer.
            foreach ($response as $event) {
                if ($event instanceof ToolCallEvent) {
                    $call = $event->toolCall;

                    $this->toolCalls[$call->id] = [
                        'id' => $call->id,
                        'name' => $call->name,
                        'arguments' => $call->arguments,
                        'status' => 'running',
                        'started_at' => $event->timestamp,
                    ];

                    continue;
                }

                if ($event instanceof ToolResultEvent && ! $event->preliminary) {
                    $callId = $event->toolResult->id;

                    if (! isset($this->toolCalls[$callId])) {
                        $this->toolCalls[$callId] = [
                            'id' => $callId,
                            'name' => $event->toolResult->name,
                            'arguments' => $event->toolResult->arguments,
                            'status' => $event->successful ? 'done' : 'failed',
                            'started_at' => $event->timestamp,
                        ];
                    }

                    $startedAt = $this->toolCalls[$callId]['started_at'];

                    $this->toolCalls[$callId]['status'] = $event->successful ? 'done' : 'failed';
                    $this->toolCalls[$callId]['result'] = $event->error
                        ?? $event->toolResult->text();
                    $this->toolCalls[$callId]['seconds'] = max(
                        0,
                        $event->timestamp - $startedAt
                    );
                }
            }

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response->text ?? '',
                'tool_calls' => array_values($this->toolCalls),
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
