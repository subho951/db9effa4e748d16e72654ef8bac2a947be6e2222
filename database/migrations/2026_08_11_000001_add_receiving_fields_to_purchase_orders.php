<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('purchase_orders')) {
            return;
        }

        if (!Schema::hasColumn('purchase_orders', 'currency_symbol')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->string('currency_symbol', 3)->default('$')->after('order_time');
            });
        }

        if (!Schema::hasColumn('purchase_orders', 'delivery_cost')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->decimal('delivery_cost', 12, 2)->default(0)->after('total_inc_tax');
            });
        }

        if (!Schema::hasColumn('purchase_orders', 'received_at')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->timestamp('received_at')->nullable()->after('delivery_cost');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('purchase_orders')) {
            return;
        }

        foreach (['received_at', 'delivery_cost', 'currency_symbol'] as $column) {
            if (Schema::hasColumn('purchase_orders', $column)) {
                Schema::table('purchase_orders', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
