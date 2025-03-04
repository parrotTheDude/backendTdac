<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone');
            $table->string('age');
            $table->string('location');
            $table->text('message');
            $table->string('preferred_contact');
            $table->boolean('is_spam')->default(false);
            $table->timestamps();
        });

        Schema::create('blocked_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_submissions');
        Schema::dropIfExists('blocked_contacts');
    }
};