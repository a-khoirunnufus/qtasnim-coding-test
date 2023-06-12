<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function product()
    {
        return view('product');
    }

    public function category()
    {
        return view('category');
    }

    public function transaction()
    {
        return view('transaction');
    }

    public function summary()
    {
        return view('summary');
    }
}
