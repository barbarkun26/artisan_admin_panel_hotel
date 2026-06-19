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
        Schema::table('checkins', function (Blueprint $table) {
            $table->string('guarantee_type')->nullable()->after('checked_in_by'); // 'deposit', 'ktp'
            $table->decimal('deposit_amount', 12, 2)->default(0)->after('guarantee_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkins', function (Blueprint $table) {
            $table->dropColumn(['guarantee_type', 'deposit_amount']);
        });
    }
};
