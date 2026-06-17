<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->dateTime('actual_checkin');
            $table->foreignId('checked_in_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->dateTime('actual_checkout');
            $table->foreignId('checked_out_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('room_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('inspected_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('inspection_date');
            $table->string('room_condition'); // e.g. Clean, Dirty, Damaged
            $table->text('missing_items')->nullable();
            $table->text('damages')->nullable();
            $table->decimal('additional_charge', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_inspections');
        Schema::dropIfExists('checkouts');
        Schema::dropIfExists('checkins');
    }
};
