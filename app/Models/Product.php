<?php

namespace App\Models;

use App\Helpers\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        $isFavorite = false;
        if (auth()->check()) {
            $isFavorite = $this->wishlists()
                ->where('user_id', auth()->id())
                ->exists();
        }

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
            'is_favorite' => $isFavorite,
            'options' => $this->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'type' => $option->type,
                    'is_required' => $option->is_required,
                    'values' => $option->values->map(function ($value) {
                        return [
                            'id' => $value->id,
                            'value' => $value->value,
                            'price' => $value->price
                        ];
                    })
                ];
            }),
            'toppings' => $this->toppings->map(function ($topping) {
                return [
                    'id' => $topping->id,
                    'name' => $topping->name,
                    'price' => $topping->price
                ];
            })
        ];
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'product_options')
            ->with('values');
    }

    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class, 'product_toppings');
    }
}
