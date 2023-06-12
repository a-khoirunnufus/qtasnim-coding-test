<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'public.transaction';

    protected $fillable = ['product_name', 'category_name', 'quantity', 'transaction_date'];

    public $timestamps = false;
}
