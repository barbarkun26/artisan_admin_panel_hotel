<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->dateTime('request_date');
            $table->string('status')->default('pending'); // pending, processing, completed
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('laundry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_request_id')->constrained('laundry_requests')->cascadeOnDelete();
            $table->string('item_name');
            $table->integer('qty');
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });

        Schema::create('fnb_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('fnb_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('fnb_categories')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('fnb_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->dateTime('order_date');
            $table->string('status')->default('pending'); // pending, processing, completed
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('fnb_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('fnb_orders')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('fnb_menus')->cascadeOnDelete();
            $table->integer('qty');
            $table->decimal('price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_order_details');
        Schema::dropIfExists('fnb_orders');
        Schema::dropIfExists('fnb_menus');
        Schema::dropIfExists('fnb_categories');
        Schema::dropIfExists('laundry_items');
        Schema::dropIfExists('laundry_requests');
    }
};
