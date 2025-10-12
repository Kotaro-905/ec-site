<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    BelongsToMany,
    HasMany
};

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $name
 * @property string|null $description
 * @property int $price
 * @property string|null $image
 * @property string|null $brand
 * @property int $status
 * @property int $condition
 *
 * @property-read Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $categories
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Like> $likes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OrderItem> $orderItems
 * @property-read string $condition_label
 */

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
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

    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 状態ラベル
    public const CONDITION_LABELS = [
        
        1 => '良好',
        2 => '目立った傷や汚れなし',
        3 => 'やや傷や汚れあり',
        4 => '状態が悪い',
    ];

    public function getConditionLabelAttribute(): string
    {
        return self::CONDITION_LABELS[$this->condition] ?? '-';
    }


    public const STATUS_ONSALE = 1;  // 出品中
    public const STATUS_SOLD   = 2;  // 売り切れ

    

    // モデル生成時のデフォルト値（マイグレーションを触らずに「出品中」を既定にしたいとき）
    protected $attributes = [
        'status' => self::STATUS_ONSALE,
    ];

    // 売り切れかどうか
    public function getIsSoldAttribute(): bool
    {
        return (int) $this->status === self::STATUS_SOLD;
    }

    // 出品中だけを取りたいとき
    public function scopeOnSale($query)
    {
        return $query->where('status', self::STATUS_ONSALE);
    }
}
