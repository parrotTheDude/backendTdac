<?php

// database/migrations/xxxx_xx_xx_create_events_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('location')->nullable();
            $table->text('details')->nullable(); // or instructions
            $table->text('instructions')->nullable();
            $table->text('booking_confirmation_message')->nullable();
            $table->json('costs')->nullable(); // if you want multiple cost lines or NDIS info
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};