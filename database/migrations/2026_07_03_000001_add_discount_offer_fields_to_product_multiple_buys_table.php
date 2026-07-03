<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_multiple_buys')) {
            return;
        }

        Schema::table('product_multiple_buys', function (Blueprint $table) {
            if (!Schema::hasColumn('product_multiple_buys', 'offer_name')) {
                $table->string('offer_name', 250)->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('product_multiple_buys', 'offer_display_name')) {
                $table->string('offer_display_name', 250)->nullable()->after('offer_name');
            }
            if (!Schema::hasColumn('product_multiple_buys', 'discount_scope')) {
                $table->enum('discount_scope', ['PRODUCT', 'BRAND'])->default('PRODUCT')->after('offer_display_name');
            }
            if (!Schema::hasColumn('product_multiple_buys', 'offer_start_date')) {
                $table->date('offer_start_date')->nullable()->after('discounted_amount');
            }
            if (!Schema::hasColumn('product_multiple_buys', 'offer_end_date')) {
                $table->date('offer_end_date')->nullable()->after('offer_start_date');
            }
            if (!Schema::hasColumn('product_multiple_buys', 'offer_no_expiry')) {
                $table->tinyInteger('offer_no_expiry')->default(0)->after('offer_end_date');
            }
            if (!Schema::hasColumn('product_multiple_buys', 'offer_start_time')) {
                $table->time('offer_start_time')->nullable()->after('offer_no_expiry');
            }
            if (!Schema::hasColumn('product_multiple_buys', 'offer_end_time')) {
                $table->time('offer_end_time')->nullable()->after('offer_start_time');
            }
            if (!Schema::hasColumn('product_multiple_buys', 'offer_available_days')) {
                $table->text('offer_available_days')->nullable()->after('offer_end_time');
            }
            if (!Schema::hasColumn('product_multiple_buys', 'offer_availability')) {
                $table->string('offer_availability', 50)->default('ALL')->after('offer_available_days');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('product_multiple_buys')) {
            return;
        }

        Schema::table('product_multiple_buys', function (Blueprint $table) {
            foreach ([
                'offer_availability',
                'offer_available_days',
                'offer_end_time',
                'offer_start_time',
                'offer_no_expiry',
                'offer_end_date',
                'offer_start_date',
                'discount_scope',
                'offer_display_name',
                'offer_name',
            ] as $column) {
                if (Schema::hasColumn('product_multiple_buys', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
