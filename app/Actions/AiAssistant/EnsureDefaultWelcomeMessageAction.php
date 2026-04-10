<?php

declare(strict_types=1);

namespace App\Actions\AiAssistant;

use App\Models\AiAssistantMessage;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final readonly class EnsureDefaultWelcomeMessageAction
{
    public function execute(User $user, ?int $beforeMessageId): void
    {
        if ($beforeMessageId !== null) {
            return;
        }

        $key = (string) config('ai_assistant.welcome.messages_config_key', 'relationship_coach.welcome_messages');
        $pool = config($key, []);
        if (! is_array($pool) || $pool === []) {
            return;
        }

        $pool = array_values(array_filter($pool, static fn ($line): bool => is_string($line) && $line !== ''));
        if ($pool === []) {
            return;
        }

        DB::transaction(function () use ($user, $pool): void {
            $exists = AiAssistantMessage::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                return;
            }

            AiAssistantMessage::query()->create([
                'user_id' => $user->id,
                'type' => AiAssistantMessage::TYPE_ASSISTANT,
                'text' => Arr::random($pool),
            ]);
        });
    }
}
