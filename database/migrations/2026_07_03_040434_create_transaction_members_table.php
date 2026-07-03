<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_members', function (Blueprint $table) {
        $table->id();
        // Menghubungkan ke kolom transaction_id di tabel induk
        $table->string('transaction_id'); 
        $table->foreign('transaction_id')->references('transaction_id')->on('transactions')->onDelete('cascade');
        
        $table->string('member_name');
        $table->decimal('base_cost', 12, 2);
        $table->decimal('tax_share', 12, 2);
        $table->decimal('discount_share', 12, 2);
        $table->decimal('extra_fee_share', 12, 2);
        $table->decimal('grand_total', 12, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_members');
    }
};
