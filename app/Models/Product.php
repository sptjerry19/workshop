<?php

namespace App\Models;

use App\Helpers\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'price',
        'size',
        'sold',
        'rating',
        'reviews',
        'description',
        'category_id',
        'stock',
        'discount'
    ];

    protected $casts = [
        'size' => 'array',
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'discount' => 'decimal:2'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transform()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => Common::responseImage($this->image),
            'price' => $this->price,
            'size' => $this->size,
            'sold' => $this->sold,
            'rating' => $this->rating,
            'reviews' => $this->reviews,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'category_name' => optional($this->category)->name,
            'stock' => $this->stock,
            'discount' => $this->discount,
        ];
    }
}
