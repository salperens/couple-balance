<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $summary
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 */
class AiAssistantChatSummary extends Model
{
    protected $table = 'ai_assistant_chat_summaries';

    protected $fillable = [
        'user_id',
        'summary',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
