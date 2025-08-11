<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'price_at_purchase',
        'status',
    ];

    protected $casts = [
        'price_at_purchase' => 'integer',
        'status'            => 'integer',
    ];

    // 購入者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 対象商品
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // この注文で使った配送先
    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
