<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $address
 * @property string $postal_code
 * @property string|null $building
 *
 * @property-read User $user
 */


class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'postal_code',
        'building',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}