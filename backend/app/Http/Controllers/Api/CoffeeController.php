<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coffee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
     * Store a newly created coffee in storage with structured logging.
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validasi Input Menggunakan Validator Manual untuk Menangkap Alur Validasi Gagal
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'sku'          => 'required|string|max:50|unique:coffees,sku',
            'origin'       => 'required|string|max:100',
            'process'      => 'required|string|max:50',
            'roast_level'  => ['required', 'string', Rule::in(['Light', 'Medium', 'Medium-Dark', 'Dark'])],
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'flavor_notes' => 'nullable|string|max:255',
        ]);

        // LOG 2 (WARNING): Alur Validasi Gagal (Konteks: Siapa, Sedang apa, Sebabnya)
        if ($validator->fails()) {
            Log::warning('Coffee product creation validation failed', [
                'actor' => [
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent() ?? 'Unknown-Client',
                    'auth_id'    => $request->user()?->id ?? 'anonymous',
                ],
                'action' => 'coffee_item_store_validation',
                'reason' => 'Request payload violated schema or business validation constraints',
                'validation_errors' => $validator->errors()->toArray(),
                'submitted_keys'    => array_keys($request->except(['password', 'token', 'secret', '_token'])),
            ]);

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 2. Eksekusi Penyimpanan ke Basis Data
        try {
            $coffee = Coffee::create($validator->validated());

            // LOG 1 (INFO): Alur Simpan Data Sukses (Konteks: Siapa, Sedang apa, Sebabnya)
            Log::info('Coffee product created and saved to database successfully', [
                'actor' => [
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent() ?? 'Unknown-Client',
                    'auth_id'    => $request->user()?->id ?? 'system_admin',
                ],
                'action' => 'coffee_item_store',
                'reason' => 'Authorized operator added a new coffee product to roastery inventory',
                'item_data' => [
                    'id'          => $coffee->id,
                    'sku'         => $coffee->sku,
                    'name'        => $coffee->name,
                    'origin'      => $coffee->origin,
                    'process'     => $coffee->process,
                    'roast_level' => $coffee->roast_level,
                    'price'       => (float) $coffee->price,
                    'stock'       => (int) $coffee->stock,
                ],
            ]);

            return response()->json([
                'message' => 'Coffee product created successfully',
                'data'    => $coffee,
            ], 201);

        } catch (\Throwable $e) {
            // LOG 3 (ERROR): Alur Gagal Menghubungi Basis Data / Query Error
            Log::error('Database error during coffee product persistence', [
                'actor' => [
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent() ?? 'Unknown-Client',
                    'auth_id'    => $request->user()?->id ?? 'system_admin',
                ],
                'action' => 'coffee_item_store_db_persist',
                'reason' => 'Database execution failed while inserting new coffee record',
                'error'  => [
                    'exception' => get_class($e),
                    'message'   => $e->getMessage(),
                ],
            ]);

            return response()->json([
                'message' => 'Failed to store coffee product due to database error',
                'error'   => $e->getMessage(),
            ], 500);
        }
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

        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'sku'          => ['required', 'string', 'max:50', Rule::unique('coffees', 'sku')->ignore($coffee->id)],
            'origin'       => 'required|string|max:100',
            'process'      => 'required|string|max:50',
            'roast_level'  => ['required', 'string', Rule::in(['Light', 'Medium', 'Medium-Dark', 'Dark'])],
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'flavor_notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('Coffee product update validation failed', [
                'actor' => [
                    'ip'      => $request->ip(),
                    'auth_id' => $request->user()?->id ?? 'anonymous',
                ],
                'action' => 'coffee_item_update_validation',
                'reason' => 'Update payload validation rules violated',
                'item_id' => $id,
                'validation_errors' => $validator->errors()->toArray(),
            ]);

            return response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $coffee->update($validator->validated());

        Log::info('Coffee product updated in inventory', [
            'actor' => [
                'ip'      => $request->ip(),
                'auth_id' => $request->user()?->id ?? 'system_admin',
            ],
            'action'  => 'coffee_item_update',
            'reason'  => 'Operator updated coffee product stock or metadata',
            'item_id' => $coffee->id,
            'sku'     => $coffee->sku,
        ]);

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

        Log::info('Coffee product deleted from inventory', [
            'action'  => 'coffee_item_delete',
            'item_id' => $id,
        ]);

        return response()->json([
            'message' => 'Coffee product deleted successfully'
        ], 200);
    }
}
