<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OptionController extends Controller
{
    public function index()
    {
        $options = Option::with('values')->get();
        return response()->json([
            'success' => true,
            'data' => $options
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:select,radio,checkbox',
            'values' => 'required|array|min:1',
            'values.*.value' => 'required|string|max:255',
            'values.*.price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $option = Option::create([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        foreach ($request->values as $valueData) {
            $option->values()->create([
                'value' => $valueData['value'],
                'price' => $valueData['price'] ?? 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Option created successfully',
            'data' => $option->load('values')
        ], 201);
    }

    public function show(Option $option)
    {
        return response()->json([
            'success' => true,
            'data' => $option->load('values')
        ]);
    }

    public function update(Request $request, Option $option)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:select,radio,checkbox',
            'values' => 'required|array|min:1',
            'values.*.value' => 'required|string|max:255',
            'values.*.price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $option->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        // Delete existing values and create new ones
        $option->values()->delete();

        foreach ($request->values as $valueData) {
            $option->values()->create([
                'value' => $valueData['value'],
                'price' => $valueData['price'] ?? 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Option updated successfully',
            'data' => $option->load('values')
        ]);
    }

    public function destroy(Option $option)
    {
        $option->values()->delete();
        $option->delete();

        return response()->json([
            'success' => true,
            'message' => 'Option deleted successfully'
        ]);
    }
}