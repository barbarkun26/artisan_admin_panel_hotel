<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaundryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'laundry_request_id',
        'item_name',
        'qty',
        'price',
    ];

    /**
     * Cast attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    /**
     * Get the laundry request.
     *
     * @return BelongsTo<LaundryRequest, $this>
     */
    public function laundryRequest(): BelongsTo
    {
        return $this->belongsTo(LaundryRequest::class);
    }
}
