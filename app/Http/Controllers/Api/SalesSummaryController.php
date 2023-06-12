<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class SalesSummaryController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'required_with:from_date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();

        $transactions = DB::table('public.transaction');

        if (isset($validated['from_date'])) {
            $transactions = $transactions->whereBetween(
                'transaction_date',
                [date($validated['from_date']), date($validated['to_date'])]
            );
        }

        $transactions = $transactions->get();

        $categories = DB::table('public.transaction')
            ->select('category_name')
            ->groupBy('category_name')
            ->get()
            ->toArray();
        $category_w_sales = array_map(function($category) {
            return [
                'category_name' => $category->category_name,
                'sales' => 0,
            ];
        }, $categories);

        foreach($category_w_sales as $key => $value) {
            $sales = 0;
            foreach($transactions as $transaction) {
                if($transaction->category_name == $value['category_name']) {
                    $sales += $transaction->quantity;
                }
            }
            $category_w_sales[$key]['sales'] = $sales;
        }

        usort($category_w_sales, function($a, $b) {
            return $b['sales'] <=> $a['sales'];
        });

        return response()->json([
            'from_date' => $validated['from_date'] ?? null,
            'to_date' => $validated['to_date'] ?? null,
            'sales_data' => $category_w_sales,
        ], 200);
    }
}
