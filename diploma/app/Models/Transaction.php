<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    const STATUS_SUCCESS = 'Success';
    const STATUS_FAIL = 'Fail';
    const STATUS_PENDING = 'Pending';

    protected $fillable = [
        'is_out',
        'sum',
        'status',
        'fee',
        'user_id',
        'payway_currency_id'
    ];
    public function paywayCurrency(): HasOne
    {
        return $this->hasOne(PaywayCurrency::class, 'id', 'payway_currency_id');
    }
}
