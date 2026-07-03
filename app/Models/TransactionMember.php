<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionMember extends Model
{
    use HasFactory;

    // Arahkan ke nama tabel anak ke-1 dengan benar
    protected $table = 'transaction_members';

    // Isi dengan kolom-kolom yang ada di migrasi tabel transaction_members
    protected $fillable = [
        'transaction_id',
        'member_name',
        'base_cost',
        'tax_share',
        'discount_share',
        'extra_fee_share',
        'grand_total'
    ];
}