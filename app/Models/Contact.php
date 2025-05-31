<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'phone',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeCustomerSupport($query)
    {
        return $query->where('type', 'customer_support');
    }

    public function transform()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'phone' => $this->phone,
            'type' => $this->type,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
    public function toArray()
    {
        return $this->transform();
    }
}
