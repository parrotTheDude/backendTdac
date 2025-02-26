<?php

// database/migrations/xxxx_xx_xx_create_bookings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // If you want to store fields like medication, pickup, etc. directly here:
            // $table->string('pickup_location')->nullable();
            // $table->string('medication')->nullable();
            // $table->text('support_needs')->nullable();
            // or you can do a separate booking_attendees table.

            $table->string('status')->default('pending'); // e.g. 'pending', 'confirmed', 'paid'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};