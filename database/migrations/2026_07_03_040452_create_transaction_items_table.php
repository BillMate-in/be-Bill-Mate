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
        Schema::create('transaction_items', function (Blueprint $table) {
        $table->id();
        // Menghubungkan ke kolom transaction_id di tabel induk
        $table->string('transaction_id');
        $table->foreign('transaction_id')->references('transaction_id')->on('transactions')->onDelete('cascade');
        
        $table->string('item_name');
        $table->decimal('price', 12, 2);
        $table->integer('qty');
        $table->decimal('total_price', 12, 2);
        $table->string('user_name'); // Siapa anggota kelompok yang makan menu ini
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
