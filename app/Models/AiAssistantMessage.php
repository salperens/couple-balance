<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 */
class AiAssistantMessage extends Model
{
    public const TYPE_USER = 'user';

    public const TYPE_ASSISTANT = 'assistant';

    protected $table = 'ai_assistant_messages';

    protected $fillable = [
        'user_id',
        'type',
        'text',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
