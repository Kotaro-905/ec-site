<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    // 出品した商品
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    // 購入（order_items）
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
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

}
