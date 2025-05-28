<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'transaction_id',
        'status',
        'payment_url',
        'payment_details'
    ];

    protected $casts = [
        'payment_details' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
