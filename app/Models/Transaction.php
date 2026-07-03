<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'transaction_id',
        'restaurant_name',
        'table_number',
        'host_name',
        'total_base_cost',
        'total_tax',
        'total_discount',
        'total_extra_fees',
        'grand_total'
    ];
}