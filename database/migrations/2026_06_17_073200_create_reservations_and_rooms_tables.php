<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
            $table->date('reservation_date');
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->integer('total_guest');
            $table->string('status')->default('pending'); // pending, checkin, checkout, cancelled, skipper
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('reservation_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->decimal('room_rate', 12, 2);
            $table->integer('extra_bed_qty')->default(0);
            $table->decimal('extra_bed_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_rooms');
        Schema::dropIfExists('reservations');
    }
};
