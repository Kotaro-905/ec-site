<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    BelongsToMany,
    HasMany
};

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 
        'name',
        'description',
        'price',
        'image',
        'brand',
        'status',
        'condition',
    ];

    protected $casts = [
        'price'     => 'integer',
        'status'    => 'integer',
        'condition' => 'integer',
    ];

    // 主カテゴリ（items.category_id）
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // 複数カテゴリ（中間表 item_categories）
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'item_categories')
            ->withTimestamps();
    }

    // コメント
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // いいね
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    // 購入明細
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
