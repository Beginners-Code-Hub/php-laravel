<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('barcode')->unique();
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('reorder_level')->default(20);
            $table->date('expiry_date');
            $table->timestamps();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('receipt_no')->unique();
            $table->decimal('total', 10, 2);
            $table->decimal('paid', 10, 2);
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('categories');
    }
};
