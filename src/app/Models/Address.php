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

    public function getPostalCodeHyphenAttribute(): string
    {
        $num = preg_replace('/\D/', '', (string) $this->postal_code);
        return preg_match('/^\d{7}$/', $num)
            ? substr($num, 0, 3) . '-' . substr($num, 3)
            : (string) $this->postal_code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
