<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 *
 * @property-read Address $address
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Like> $likes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OrderItem> $orderItems
 */

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, Notifiable;

    use Notifiable;
    
    use MustVerifyEmailTrait;


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
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
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

    public function address(): HasOne
    {
       
        return $this->hasOne(Address::class);
        
    }


}
