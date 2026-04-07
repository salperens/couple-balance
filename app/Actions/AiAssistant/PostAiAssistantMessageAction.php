<?php

declare(strict_types=1);

namespace App\Actions\AiAssistant;

use App\Models\AiAssistantMessage;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final readonly class PostAiAssistantMessageAction
{
    private const MOCK_REPLIES = [
        'Bunu duyduğuma üzüldüm; burada olduğunu bil.',
        'Duygularını paylaştığın için teşekkürler. Birlikte üzerinde düşünebiliriz.',
        'Bu sana ağır gelmiş olabilir. Nefes almana izin ver.',
        'Haklı olabileceğin noktalar var; kendine karşı nazik olmayı dene.',
        'Şu an için küçük bir adım bile yeterli olabilir.',
    ];

    /**
     * @return array{user: AiAssistantMessage, assistant: AiAssistantMessage}
     */
    public function execute(User $user, string $text): array
    {
        return DB::transaction(function () use ($user, $text): array {
            $userMessage = AiAssistantMessage::query()->create([
                'user_id' => $user->id,
                'type'    => AiAssistantMessage::TYPE_USER,
                'text'    => $text,
            ]);

            $assistantMessage = AiAssistantMessage::query()->create([
                'user_id' => $user->id,
                'type'    => AiAssistantMessage::TYPE_ASSISTANT,
                'text'    => Arr::random(self::MOCK_REPLIES),
            ]);

            return [
                'user'      => $userMessage,
                'assistant' => $assistantMessage,
            ];
        });
    }
}
