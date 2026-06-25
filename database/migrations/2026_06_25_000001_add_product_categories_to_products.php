<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_categories')) {
            Schema::create('product_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 250)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('category_id')->default(0)->after('brand_id');
            });
        }

        $examples = [
            'Scotch Whisky',
            'Irish Whiskey',
            'Gin',
            'Brandy',
            'Cognac',
            'Rum',
        ];

        foreach ($examples as $example) {
            $exists = DB::table('product_categories')
                ->where('name', $example)
                ->where('status', '!=', 3)
                ->exists();

            if (!$exists) {
                DB::table('product_categories')->insert([
                    'name'       => $example,
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
        }

        Schema::dropIfExists('product_categories');
    }
};
