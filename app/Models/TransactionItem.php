<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    // Arahkan ke nama tabel anak ke-2 dengan benar
    protected $table = 'transaction_items';

    // Isi dengan kolom-kolom yang ada di migrasi tabel transaction_items
    protected $fillable = [
        'transaction_id',
        'item_name',
        'price',
        'qty',
        'total_price',
        'user_name'
    ];
}