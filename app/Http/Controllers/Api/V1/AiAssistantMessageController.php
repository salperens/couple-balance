<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\AiAssistant\ListAiAssistantMessagesAction;
use App\Actions\AiAssistant\PostAiAssistantMessageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AiAssistant\ListAiAssistantMessagesRequest;
use App\Http\Requests\Api\V1\AiAssistant\StoreAiAssistantMessageRequest;
use App\Models\AiAssistantMessage;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AiAssistantMessageController extends Controller
{
    public function __construct(
        private readonly ListAiAssistantMessagesAction $listAiAssistantMessagesAction,
        private readonly PostAiAssistantMessageAction $postAiAssistantMessageAction,
    ) {
    }

    public function index(ListAiAssistantMessagesRequest $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->limitValue();
        $beforeId = $request->beforeMessageId();

        $payload = $this->listAiAssistantMessagesAction->execute($user, $limit, $beforeId);

        $nextUrl = $payload['nextCursor'] !== null
            ? $this->buildMessagesUrl($limit, $payload['nextCursor'])
            : null;

        return response()->json([
            'messages' => $payload['messages']->map(fn (AiAssistantMessage $m) => $this->messageToArray($m))->values()->all(),
            'pagination' => [
                'hasMore'     => $payload['hasMore'],
                'nextCursor'  => $payload['nextCursor'],
                'nextUrl'     => $nextUrl,
            ],
            'chatState' => $this->mockChatState($payload['messageCount']),
        ]);
    }

    public function store(StoreAiAssistantMessageRequest $request): JsonResponse
    {
        $created = $this->postAiAssistantMessageAction->execute(
            $request->user(),
            $request->validated('text')
        );

        return response()->json([
            'userMessage'      => $this->messageToArray($created['user']),
            'assistantMessage' => $this->messageToArray($created['assistant']),
        ], Response::HTTP_CREATED);
    }

    /**
     * @return array{id: int, type: string, text: string, createdAt: string}
     */
    private function messageToArray(AiAssistantMessage $message): array
    {
        return [
            'id'        => $message->id,
            'type'      => $message->type,
            'text'      => $message->text,
            'createdAt' => $message->created_at->clone()->utc()->format('Y-m-d\TH:i:s\Z'),
        ];
    }

    private function buildMessagesUrl(int $limit, string $cursor): string
    {
        $query = http_build_query([
            'limit'  => $limit,
            'cursor' => $cursor,
        ]);

        return '/ai-assistant/messages?'.$query;
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
    private function mockChatState(int $messageCount): array
    {
        $mod = $messageCount % 10;

        return [
            'messageCount'                => $messageCount,
            'remainingUntilNextSummary' => $mod === 0 ? 10 : 10 - $mod,
            'topic'                       => 'relationship_conflict',
            'emotion'                     => 'neutral',
            'summaryCreated'              => false,
        ];
    }
}
