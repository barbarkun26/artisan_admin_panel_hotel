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
        Schema::table('guests', function (Blueprint $table) {
            $table->string('profession')->nullable()->after('address');
            $table->string('company')->nullable()->after('profession');
            $table->string('nationality')->nullable()->after('company');
            $table->date('birth_date')->nullable()->after('nationality');
            $table->string('member_card_no')->nullable()->after('birth_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn([
                'profession',
                'company',
                'nationality',
                'birth_date',
                'member_card_no',
            ]);
        });
    }
};
