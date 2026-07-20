<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // 1. Add new columns
            $table->string('item_no', 50)->nullable()->after('company_id');
            $table->decimal('stock', 10, 2)->default(0)->after('description');
            $table->string('stock_unit', 20)->default('Mtr')->after('stock');
            $table->string('stock_deduction_type', 20)->default('Meter')->after('stock_unit');
            $table->decimal('consumption_per_piece', 10, 2)->nullable()->after('stock_deduction_type');
            $table->decimal('minimum_stock', 10, 2)->default(0)->after('consumption_per_piece');
            $table->decimal('selling_price', 10, 2)->nullable()->after('minimum_stock');

            // 2. Unique index on (company_id, item_no) after adding column
            $table->unique(['company_id', 'item_no']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'item_no']);
            $table->dropColumn([
                'item_no',
                'stock',
                'stock_unit',
                'stock_deduction_type',
                'consumption_per_piece',
                'minimum_stock',
                'selling_price',
            ]);
        });
    }
};