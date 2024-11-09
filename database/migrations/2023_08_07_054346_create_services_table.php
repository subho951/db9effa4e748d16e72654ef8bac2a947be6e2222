<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->string('slug')->nullable();

            $table->string('description')->nullable();

            $table->string('image')->nullable();

            $table->string('mentor_bg_color', 10)->nullable();

            $table->boolean('status')->default(1);

            $table->timestamp('created_at')->useCurrent();

            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
