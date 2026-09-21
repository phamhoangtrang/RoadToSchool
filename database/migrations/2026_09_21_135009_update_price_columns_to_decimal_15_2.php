<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('origin_price', 15, 2)->change();
            $table->decimal('promotion_price', 15, 2)->nullable()->change();
        });

        Schema::table('bill_course', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('origin_price', 8, 2)->change();
            $table->decimal('promotion_price', 8, 2)->nullable()->change();
        });

        Schema::table('bill_course', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->change();
        });
    }
};
