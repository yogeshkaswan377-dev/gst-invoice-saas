<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50); // invoice_create, invoice_edit, invoice_delete, manual_add, manual_deduct, manual_set, csv_import
            $table->string('stock_deduction_type', 20)->nullable();
            $table->decimal('invoice_quantity', 10, 2)->nullable();
            $table->decimal('consumed_stock', 10, 2)->nullable();
            $table->decimal('previous_stock', 10, 2);
            $table->decimal('current_stock', 10, 2);
            $table->string('reference_type', 50)->nullable(); // invoice, manual, csv
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
