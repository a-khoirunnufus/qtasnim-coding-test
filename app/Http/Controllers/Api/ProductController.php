<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'nullable',
            'sort_by' => 'nullable|in:product_name,category_name,quantity,sold,available',
            'order_by' => 'required_with:sort_by|in:asc,desc',
            'offset' => 'nullable|integer',
            'limit' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();

        $query = DB::table('public.product as p')
            ->leftJoin('public.product_category as c', 'c.id', '=', 'p.category_id');

        if (isset($validated['query'])) {
            $query = $query->where('p.product_name', 'ilike', "%{$validated['query']}%")
                ->orWhere('c.category_name', 'ilike', "%{$validated['query']}%");
        }

        if (isset($validated['sort_by'])) {
            $sort_by_process = 'p.'.$validated['sort_by'];
            if($validated['sort_by'] == 'category_name') {
                $sort_by_process = 'c.category_name';
            }
            $query = $query->orderBy($sort_by_process, $validated['order_by']);
        }

        if (isset($validated['offset'])) {
            $query = $query->skip($validated['offset']);
        }

        if (isset($validated['limit'])) {
            $query = $query->take($validated['limit']);
        }

        $data = $query
            ->select(
                'p.id',
                'p.product_name',
                'p.category_id',
                'c.category_name',
                'p.quantity',
                'p.sold',
                'p.available',
                'p.created_at',
                'p.updated_at',
                'p.deleted_at'
            )
            ->get()
            ->toArray();

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'category_id' => 'required|integer|exists:App\Models\ProductCategory,id',
            'quantity' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $product = Product::create([
                'product_name' => $validated['product_name'],
                'category_id' => $validated['category_id'],
                'quantity' => $validated['quantity'],
                'sold' => 0,
                'available' => $validated['quantity'],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'error' => config('app.env') == 'production'
                    ? 'Terjadi kesalahan.'
                    : $th->getMessage(),
            ], 500);
        }

        return response()->json($product, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $product_id)
    {
        $product = Product::find($product_id);

        if ($product == null) {
            return response()->json([
                'error' => 'Produk dengan id ini tidak tersedia.',
            ], 404);
        }

        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $product_id)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'nullable',
            'category_id' => 'nullable|integer|exists:App\Models\ProductCategory,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();

        if (Product::find($product_id) == null) {
            return response()->json([
                'error' => 'Produk dengan id ini tidak tersedia.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            Product::where('id', '=', $product_id)->update($validated);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'error' => config('app.env') == 'production'
                    ? 'Terjadi kesalahan.'
                    : $th->getMessage(),
            ], 500);
        }

        return response()->json(Product::find($product_id), 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $product_id)
    {
        $product = Product::find($product_id);
        if ($product == null) {
            return response()->json([
                'error' => 'Produk dengan id ini tidak tersedia.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            $product->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'error' => config('app.env') == 'production'
                    ? 'Terjadi kesalahan.'
                    : $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => 'Berhasil menghapus produk.',
        ], 200);
    }
}
