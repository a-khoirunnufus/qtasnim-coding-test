<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable',
            'sort_by' => 'nullable|in:category_name',
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

        $query = DB::table('public.product_category');

        if (isset($validated['search'])) {
            $query = $query->where('category_name', 'ilike', "%{$validated['search']}%");
        }

        if (isset($validated['sort_by'])) {
            $query = $query->orderBy($validated['sort_by'], $validated['order_by']);
        }

        if (isset($validated['offset'])) {
            $query = $query->skip($validated['offset']);
        }

        if (isset($validated['limit'])) {
            $query = $query->take($validated['limit']);
        }

        $data = $query->get()->toArray();

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $category = ProductCategory::create([
                'category_name' => $validated['category_name'],
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

        return response()->json($category, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $category_id)
    {
        $category = ProductCategory::find($category_id);

        if ($category == null) {
            return response()->json([
                'error' => 'Kategori dengan id ini tidak tersedia.',
            ], 404);
        }

        return response()->json($category, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $category_id)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();

        if (ProductCategory::find($category_id) == null) {
            return response()->json([
                'error' => 'Kategori dengan id ini tidak tersedia.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            ProductCategory::where('id', '=', $category_id)->update($validated);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'error' => config('app.env') == 'production'
                    ? 'Terjadi kesalahan.'
                    : $th->getMessage(),
            ], 500);
        }

        return response()->json(ProductCategory::find($category_id), 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $category_id)
    {
        $category = ProductCategory::find($category_id);
        if ($category == null) {
            return response()->json([
                'error' => 'Kategori dengan id ini tidak tersedia.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            $category->delete();

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
            'success' => 'Berhasil menghapus kategori.',
        ], 200);
    }
}
