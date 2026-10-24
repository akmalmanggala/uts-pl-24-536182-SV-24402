<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coffee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CoffeeController extends Controller
{
    /**
     * Display a listing of coffees.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Coffee::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('origin', 'like', "%{$search}%")
                  ->orWhere('flavor_notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('process')) {
            $query->where('process', $request->input('process'));
        }

        if ($request->filled('roast_level')) {
            $query->where('roast_level', $request->input('roast_level'));
        }

        $coffees = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Coffee catalogue fetched successfully',
            'data'    => $coffees,
            'count'   => $coffees->count(),
        ], 200);
    }

    /**
     * Store a newly created coffee in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'sku'          => 'required|string|max:50|unique:coffees,sku',
            'origin'       => 'required|string|max:100',
            'process'      => 'required|string|max:50',
            'roast_level'  => ['required', 'string', Rule::in(['Light', 'Medium', 'Medium-Dark', 'Dark'])],
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'flavor_notes' => 'nullable|string|max:255',
        ]);

        $coffee = Coffee::create($validated);

        return response()->json([
            'message' => 'Coffee product created successfully',
            'data'    => $coffee,
        ], 201);
    }

    /**
     * Display the specified coffee.
     */
    public function show(int $id): JsonResponse
    {
        $coffee = Coffee::find($id);

        if (!$coffee) {
            return response()->json([
                'message' => 'Coffee product not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Coffee product details fetched successfully',
            'data'    => $coffee,
        ], 200);
    }

    /**
     * Update the specified coffee in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $coffee = Coffee::find($id);

        if (!$coffee) {
            return response()->json([
                'message' => 'Coffee product not found'
            ], 404);
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'sku'          => ['required', 'string', 'max:50', Rule::unique('coffees', 'sku')->ignore($coffee->id)],
            'origin'       => 'required|string|max:100',
            'process'      => 'required|string|max:50',
            'roast_level'  => ['required', 'string', Rule::in(['Light', 'Medium', 'Medium-Dark', 'Dark'])],
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'flavor_notes' => 'nullable|string|max:255',
        ]);

        $coffee->update($validated);

        return response()->json([
            'message' => 'Coffee product updated successfully',
            'data'    => $coffee,
        ], 200);
    }

    /**
     * Remove the specified coffee from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $coffee = Coffee::find($id);

        if (!$coffee) {
            return response()->json([
                'message' => 'Coffee product not found'
            ], 404);
        }

        $coffee->delete();

        return response()->json([
            'message' => 'Coffee product deleted successfully'
        ], 200);
    }
}
