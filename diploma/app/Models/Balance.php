<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;
    protected $fillable = [
        'currency_id',
        'user_id',
        'value',
    ];
    protected $casts = [
        'value' => 'double'
    ];
}
