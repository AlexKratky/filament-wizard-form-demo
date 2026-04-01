<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_orders', function (Blueprint $table) {
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('demo_orders', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'ended_at', 'base_price', 'tax_rate']);
        });
    }
};
