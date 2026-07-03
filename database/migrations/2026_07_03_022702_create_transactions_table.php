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
        Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('transaction_id')->unique(); // Pemicu relasi utama
        $table->string('restaurant_name');
        $table->string('table_number');
        $table->string('host_name');
        $table->decimal('total_base_cost', 12, 2);
        $table->decimal('total_tax', 12, 2);
        $table->decimal('total_discount', 12, 2);
        $table->decimal('total_extra_fees', 12, 2);
        $table->decimal('grand_total', 12, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
