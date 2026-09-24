<?php

namespace App\Livewire\Page\Main\AI;

use App\Ai\Agents\HRAssistant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Laravel\Ai\Contracts\ConversationStore;
use Throwable;

#[Layout('layouts.main', ['title' => 'HRWork AI Assistant'])]
class Assistant extends Component
{
    public string $prompt = '';

    public ?string $conversationId = null;

    public bool $isLoading = false;

    /** @var array<int, array{role:string, content:string}> */
    public array $messages = [];

    public function send(): void
    {
        $this->validate([
            'prompt' => ['required', 'string', 'max:10000'],
        ]);

        $this->isLoading = true;

        try {
            $response = HRAssistant::make()
                ->continueOrStart(
                    $this->conversationId,
                    as: Auth::user()
                )
                ->prompt(
                    trim($this->prompt),
                    provider: '9router',
                    model: config(
                        'ai.providers.9router.models.text.default'
                    ),
                );

            $this->conversationId = $response->conversationId;

            $this->loadMessages();
            $this->prompt = '';
        } catch (Throwable $exception) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'AI gagal memproses permintaan',
                message: $exception->getMessage()
            );
        } finally {
            $this->isLoading = false;
        }
    }

    public function newConversation(): void
    {
        $this->conversationId = null;
        $this->messages = [];
        $this->prompt = '';
    }

    public function openConversation(string $conversationId): void
    {
        $conversationExists = Auth::user()
            ->conversations()
            ->whereKey($conversationId)
            ->where('agent', HRAssistant::class)
            ->exists();

        if (! $conversationExists) {
            return;
        }

        $this->conversationId = $conversationId;
        $this->loadMessages();
    }

    private function loadMessages(): void
    {
        if ($this->conversationId === null) {
            $this->messages = [];

            return;
        }

        $this->messages = app(ConversationStore::class)
            ->getLatestConversationMessages(
                $this->conversationId,
                100
            )
            ->reverse()
            ->map(fn ($message) => [
                'role' => $message->role->value,
                'content' => $message->content ?? '',
            ])
            ->values()
            ->all();
    }

    public function render()
    {
        return view(
            'livewire.page.main.ai.assistant',
            [
                'conversations' => Auth::user()
                    ->conversations()
                    ->where('agent', HRAssistant::class)
                    ->latest('updated_at')
                    ->limit(12)
                    ->get(),
            ]
        );
    }
}
