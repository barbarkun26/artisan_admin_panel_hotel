<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FnbMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'description',
        'active',
    ];

    /**
     * Cast attributes.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    /**
     * Get the category of this menu.
     *
     * @return BelongsTo<FnbCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FnbCategory::class, 'category_id');
    }

    /**
     * Get order details utilizing this menu item.
     *
     * @return HasMany<FnbOrderDetail, $this>
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(FnbOrderDetail::class, 'menu_id');
    }
}
