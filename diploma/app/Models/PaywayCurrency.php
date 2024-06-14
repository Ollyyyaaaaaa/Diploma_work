<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaywayCurrency extends Model
{
    use HasFactory;
    protected $fillable = [
        'currency_id',
        'payway_id',
        'is_active',
        'max',
        'min',
        'fee'
    ];
    protected $casts = [
        'max' => 'double',
        'min' => 'double',
        'fee' => 'double'
    ];

    public function currency(): HasOne
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }
    public function payway(): HasOne
    {
        return $this->hasOne(Payway::class, 'id', 'payway_id');
    }
}
