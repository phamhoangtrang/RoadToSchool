<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cột tổng tiền hóa đơn trước đây là decimal(8,2) (tối đa 999.999,99),
     * không đủ cho đơn từ 1.000.000 VNĐ. Đồng bộ với các cột giá khác.
     */
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->decimal('total_amount', 8, 2)->change();
        });
    }
};
