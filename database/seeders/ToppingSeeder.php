<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Seeder;

class ToppingSeeder extends Seeder
{
    public function run(): void
    {
        $toppings = [
            [
                'name' => 'Whipped Cream',
                'price' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Caramel Sauce',
                'price' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Chocolate Sauce',
                'price' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Vanilla Syrup',
                'price' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Extra Espresso Shot',
                'price' => 10000,
                'is_active' => true,
            ],
            [
                'name' => 'Cinnamon Powder',
                'price' => 3000,
                'is_active' => true,
            ],
            [
                'name' => 'Coconut Flakes',
                'price' => 5000,
                'is_active' => true,
            ],
            [
                'name' => 'Cookie Crumbs',
                'price' => 5000,
                'is_active' => true,
            ],
        ];

        foreach ($toppings as $topping) {
            Topping::create($topping);
        }
    }
}
