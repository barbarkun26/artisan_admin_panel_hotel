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
        Schema::table('fnb_orders', function (Blueprint $table) {
            $table->string('payment_type')->default('billed_to_room')->after('total_amount'); // 'on_the_spot', 'billed_to_room'
        });

        Schema::table('laundry_requests', function (Blueprint $table) {
            $table->string('payment_type')->default('billed_to_room')->after('total_amount'); // 'on_the_spot', 'billed_to_room'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fnb_orders', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });

        Schema::table('laundry_requests', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });
    }
};
