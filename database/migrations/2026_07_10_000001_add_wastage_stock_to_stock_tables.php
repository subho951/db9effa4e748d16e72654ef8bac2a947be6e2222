<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'wastage_stock')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('wastage_stock')->default(0)->after('warehouse_stock');
            });
        }

        if (!Schema::hasColumn('shop_stocks', 'wastage_qty')) {
            Schema::table('shop_stocks', function (Blueprint $table) {
                $table->integer('wastage_qty')->default(0)->after('txn_qty');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('shop_stocks', 'wastage_qty')) {
            Schema::table('shop_stocks', function (Blueprint $table) {
                $table->dropColumn('wastage_qty');
            });
        }

        if (Schema::hasColumn('products', 'wastage_stock')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('wastage_stock');
            });
        }
    }
};
