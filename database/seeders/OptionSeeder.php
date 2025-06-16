<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        // Temperature Option
        $temperature = Option::create([
            'name' => 'Temperature',
            'type' => 'radio',
            'is_required' => true,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $temperature->id,
            'value' => 'Hot',
            'price' => 0,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $temperature->id,
            'value' => 'Cold',
            'price' => 0,
            'is_active' => true,
        ]);

        // Ice Level Option
        $iceLevel = Option::create([
            'name' => 'Ice Level',
            'type' => 'radio',
            'is_required' => true,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $iceLevel->id,
            'value' => 'No Ice',
            'price' => 0,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $iceLevel->id,
            'value' => 'Less Ice',
            'price' => 0,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $iceLevel->id,
            'value' => 'Normal Ice',
            'price' => 0,
            'is_active' => true,
        ]);

        // Sugar Level Option
        $sugarLevel = Option::create([
            'name' => 'Sugar Level',
            'type' => 'radio',
            'is_required' => true,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $sugarLevel->id,
            'value' => 'No Sugar',
            'price' => 0,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $sugarLevel->id,
            'value' => '25%',
            'price' => 0,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $sugarLevel->id,
            'value' => '50%',
            'price' => 0,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $sugarLevel->id,
            'value' => '75%',
            'price' => 0,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $sugarLevel->id,
            'value' => '100%',
            'price' => 0,
            'is_active' => true,
        ]);

        // Milk Type Option
        $milkType = Option::create([
            'name' => 'Milk Type',
            'type' => 'radio',
            'is_required' => false,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $milkType->id,
            'value' => 'Fresh Milk',
            'price' => 0,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $milkType->id,
            'value' => 'Oat Milk',
            'price' => 10000,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $milkType->id,
            'value' => 'Almond Milk',
            'price' => 10000,
            'is_active' => true,
        ]);

        OptionValue::create([
            'option_id' => $milkType->id,
            'value' => 'Soy Milk',
            'price' => 10000,
            'is_active' => true,
        ]);
    }
}
