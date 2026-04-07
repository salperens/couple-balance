<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

final class AiAssistantMessageCursor
{
    /**
     * @return array{lastMessageId: int}
     */
    public static function decode(string $cursor): array
    {
        $raw = base64_decode($cursor, true);
        if ($raw === false) {
            throw new InvalidArgumentException('Invalid cursor encoding.');
        }

        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['lastMessageId']) || !is_numeric($data['lastMessageId'])) {
            throw new InvalidArgumentException('Invalid cursor payload.');
        }

        $id = (int)$data['lastMessageId'];
        if ($id < 1) {
            throw new InvalidArgumentException('Invalid cursor payload.');
        }

        return ['lastMessageId' => $id];
    }

    public static function encode(int $lastMessageId): string
    {
        return base64_encode(json_encode(['lastMessageId' => $lastMessageId], JSON_THROW_ON_ERROR));
    }
}
