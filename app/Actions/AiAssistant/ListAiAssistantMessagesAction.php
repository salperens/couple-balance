<?php

declare(strict_types=1);

namespace App\Actions\AiAssistant;

use App\Models\AiAssistantMessage;
use App\Models\User;
use App\Support\AiAssistantMessageCursor;

final readonly class ListAiAssistantMessagesAction
{
    /**
     * @return array{
     *     messages: \Illuminate\Support\Collection<int, AiAssistantMessage>,
     *     hasMore: bool,
     *     nextCursor: string|null,
     *     messageCount: int
     * }
     */
    public function execute(User $user, int $limit, ?int $beforeMessageId): array
    {
        $baseQuery = AiAssistantMessage::query()->where('user_id', $user->id);

        $messageCount = (clone $baseQuery)->count();

        $query = (clone $baseQuery)->orderByDesc('id');
        if ($beforeMessageId !== null) {
            $query->where('id', '<', $beforeMessageId);
        }

        $rows = $query->limit($limit + 1)->get();
        $hasMore = $rows->count() > $limit;
        if ($hasMore) {
            $rows = $rows->take($limit);
        }

        $oldest = $rows->last();
        $nextCursor = ($hasMore && $oldest !== null)
            ? AiAssistantMessageCursor::encode($oldest->id)
            : null;

        return [
            'messages'     => $rows,
            'hasMore'      => $hasMore,
            'nextCursor'   => $nextCursor,
            'messageCount' => $messageCount,
        ];
    }
}
