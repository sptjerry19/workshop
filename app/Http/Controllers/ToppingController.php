<?php

namespace App\Http\Controllers;

use App\Models\Topping;
use Illuminate\Http\Request;

class ToppingController extends Controller
{
    public function index()
    {
        $toppings = Topping::where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'data' => $toppings
        ]);
    }
}
