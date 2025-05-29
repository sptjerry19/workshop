<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'content',
        'is_sent',
        'sent_at'
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'sent_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
