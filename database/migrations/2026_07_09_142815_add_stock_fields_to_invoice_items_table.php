<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Only add consumed_stock if it doesn't exist
            if (!Schema::hasColumn('invoice_items', 'consumed_stock')) {
                $table->decimal('consumed_stock', 10, 2)->nullable()->after('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'consumed_stock']);
        });
    }
};
