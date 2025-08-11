<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    // status と condition は tinyInteger
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'brand',
        'description',
        'price',
        'image',
        'condition',
        'status',
    ];

    protected $casts = [
        'price'     => 'integer',
        'condition' => 'integer',
        'status'    => 'integer',
    ];

    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // カテゴリ
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 購入（この商品に紐づく注文）
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    // 便利スコープ
    public function scopeOwnedBy($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopePublished($q) // 1=公開
    {
        return $q->where('status', 1);
    }

    public function scopeWithCondition($q, int $condition)
    {
        return $q->where('condition', $condition);
    }
}
