<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasMany,
    BelongsToMany
};

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];


    public function primaryItems(): HasMany
    {
        return $this->hasMany(Item::class);
    }


    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_categories')
            ->withTimestamps();
    }
}
