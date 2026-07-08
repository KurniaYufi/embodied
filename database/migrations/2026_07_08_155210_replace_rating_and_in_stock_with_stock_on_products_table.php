<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->after('gradient');
        });

        // Backfill: products previously flagged in_stock get a starting quantity
        // instead of silently becoming "0 in stock" once the column is dropped.
        DB::table('products')->where('in_stock', true)->update(['stock' => 10]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['rating', 'reviews_count', 'in_stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->boolean('in_stock')->default(true);
        });

        DB::table('products')->where('stock', '<=', 0)->update(['in_stock' => false]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
