<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FnbCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get menus belonging to this category.
     *
     * @return HasMany<FnbMenu, $this>
     */
    public function menus(): HasMany
    {
        return $this->hasMany(FnbMenu::class, 'category_id');
    }
}
