<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'module',
        'action',
        'description',
    ];

    /**
     * Cast attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the staff user who triggered the activity.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an activity.
     *
     * @param int|null $userId
     * @param string $module
     * @param string $action
     * @param string $description
     * @return self
     */
    public static function log(?int $userId, string $module, string $action, string $description): self
    {
        return self::create([
            'user_id' => $userId,
            'module' => $module,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
