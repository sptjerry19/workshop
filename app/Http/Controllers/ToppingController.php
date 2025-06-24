<?php

namespace App\Http\Controllers;

use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function indexAdmin()
    {
        $toppings = Topping::query()->orderByDesc('created_at')->get();
        return response()->json([
            'success' => true,
            'data' => $toppings
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $topping = Topping::create([
            'name' => $request->name,
            'price' => $request->price,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Topping created successfully',
            'data' => $topping
        ], 201);
    }

    public function show(Topping $topping)
    {
        return response()->json([
            'success' => true,
            'data' => $topping
        ]);
    }

    public function update(Request $request, Topping $topping)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $topping->update([
            'name' => $request->name,
            'price' => $request->price,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Topping updated successfully',
            'data' => $topping
        ]);
    }

    public function destroy(Topping $topping)
    {
        $topping->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topping deleted successfully'
        ]);
    }
}
