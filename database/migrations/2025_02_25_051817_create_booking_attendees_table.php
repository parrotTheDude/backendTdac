<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('booking_attendees', function (Blueprint $table) {
            $table->id();

            // Link to the booking
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            
            // Attendee fields (examples)
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('pickup_location')->nullable();
            $table->string('medication')->nullable();
            $table->string('companion_card_holder')->nullable();
            $table->text('support_needs')->nullable();
            $table->text('additional_support_info')->nullable();
            $table->text('family_friends_info')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_attendees');
    }
};