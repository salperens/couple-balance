<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\AiAssistant\EnsureDefaultWelcomeMessageAction;
use App\Actions\AiAssistant\ListAiAssistantMessagesAction;
use App\Actions\AiAssistant\PostAiAssistantMessageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AiAssistant\ListAiAssistantMessagesRequest;
use App\Http\Requests\Api\V1\AiAssistant\StoreAiAssistantMessageRequest;
use App\Models\AiAssistantMessage;
use App\Models\User;
use App\Support\AiAssistant\AiAssistantLimits;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AiAssistantMessageController extends Controller
{
    public function __construct(
        private readonly EnsureDefaultWelcomeMessageAction $ensureDefaultWelcomeMessageAction,
        private readonly ListAiAssistantMessagesAction $listAiAssistantMessagesAction,
        private readonly PostAiAssistantMessageAction $postAiAssistantMessageAction,
    ) {}

    public function index(ListAiAssistantMessagesRequest $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->limitValue();
        $beforeId = $request->beforeMessageId();

        $this->ensureDefaultWelcomeMessageAction->execute($user, $beforeId);

        $payload = $this->listAiAssistantMessagesAction->execute($user, $limit, $beforeId);

        $nextUrl = $payload['nextCursor'] !== null
            ? $this->buildMessagesUrl($limit, $payload['nextCursor'])
            : null;

        return response()->json([
            'messages' => $payload['messages']->map(fn (AiAssistantMessage $m) => $this->messageToArray($m))->values()->all(),
            'pagination' => [
                'hasMore' => $payload['hasMore'],
                'nextCursor' => $payload['nextCursor'],
                'nextUrl' => $nextUrl,
            ],
            'chatState' => $this->chatState($user, $payload['messageCount']),
        ]);
    }

    public function store(StoreAiAssistantMessageRequest $request): JsonResponse
    {
        $created = $this->postAiAssistantMessageAction->execute(
            $request->user(),
            $request->validated('text')
        );

        return response()->json([
            'userMessage' => $this->messageToArray($created['user']),
            'assistantMessage' => $this->messageToArray($created['assistant']),
        ], Response::HTTP_CREATED);
    }

    /**
     * @return array{id: int, type: string, text: string, createdAt: string}
     */
    private function messageToArray(AiAssistantMessage $message): array
    {
        return [
            'id' => $message->id,
            'type' => $message->type,
            'text' => $message->text,
            'createdAt' => $message->created_at->clone()->utc()->format('Y-m-d\TH:i:s\Z'),
        ];
    }

    private function buildMessagesUrl(int $limit, string $cursor): string
    {
        $path = $this->messagesApiPath();

        $query = http_build_query([
            'limit' => $limit,
            'cursor' => $cursor,
        ]);

        return $path.'?'.$query;
    }

    private function messagesApiPath(): string
    {
        return (string) config('ai_assistant.api.messages_path', '/ai-assistant/messages');
    }

    /**
     * @return array{
     *     messageCount: int,
     *     remainingUntilNextSummary: int,
     *     topic: string,
     *     emotion: string,
     *     summaryCreated: bool
     * }
     */
    private function chatState(User $user, int $messageCount): array
    {
        $interval = (int) config('ai_assistant.limits.summary_interval', AiAssistantLimits::SUMMARY_INTERVAL);
        $interval = $interval > 0 ? $interval : AiAssistantLimits::SUMMARY_INTERVAL;

        $mod = $messageCount % $interval;
        $hasSummary = $user->aiAssistantChatSummary()->exists();

        return [
            'messageCount' => $messageCount,
            'remainingUntilNextSummary' => $mod === 0 ? $interval : $interval - $mod,
            'topic' => (string) config('ai_assistant.chat_state.default_topic'),
            'emotion' => (string) config('ai_assistant.chat_state.default_emotion'),
            'summaryCreated' => $hasSummary,
        ];
    }
}
