<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable',
            'sort_by' => 'nullable|in:product_name,category_name,quantity,transaction_date',
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

        $query = DB::table('public.transaction');

        if (isset($validated['search'])) {
            $query = $query->where('product_name', 'ilike', "%{$validated['search']}%")
                ->orWhere('category_name', 'ilike', "%{$validated['search']}%")
                ->orWhere('transaction_date', 'ilike', "%{$validated['search']}%");
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

        $data = $query
            ->select(
                'id',
                'product_name',
                'category_name',
                'quantity',
                'transaction_date'
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
            'product_id' => 'required|exists:App\Models\Product,id',
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();

        $product = Product::find($validated['product_id']);

        if ($product->available < $validated['quantity']) {
            return response()->json([
                'error' => 'Stok produk tidak cukup.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $product->available = $product->available - $validated['quantity'];
            $product->sold = $product->sale + $validated['quantity'];
            $product->save();

            $transaction = Transaction::create([
                'product_name' => $product->product_name,
                'category_name' => $product->category->category_name,
                'quantity' => $validated['quantity'],
                'transaction_date' => $validated['transaction_date'],
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

        return response()->json($transaction, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $transaction_id)
    {
        $transaction = Transaction::find($transaction_id);

        if ($transaction == null) {
            return response()->json([
                'error' => 'Transaksi dengan id ini tidak tersedia.',
            ], 404);
        }

        return response()->json($transaction, 200);
    }
}
