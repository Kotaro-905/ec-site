<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'purchase_id',
        'user_id',
        'address',
        'postal_code',
        'building',
    ];

    // 所有ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 紐づく購入（購入時に固定した住所の場合のみ）
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
