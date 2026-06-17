<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FnbOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'qty',
        'price',
        'subtotal',
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
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Get the F&B order.
     *
     * @return BelongsTo<FnbOrder, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(FnbOrder::class, 'order_id');
    }

    /**
     * Get the F&B menu item.
     *
     * @return BelongsTo<FnbMenu, $this>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(FnbMenu::class, 'menu_id');
    }
}
