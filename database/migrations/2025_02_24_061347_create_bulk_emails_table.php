<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('bulk_emails', function (Blueprint $table) {
        $table->id();
        $table->string('template_id');
        $table->string('template_name');
        $table->json('variables')->nullable();
        $table->json('recipient_lists');
        $table->integer('emails_sent')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_emails');
    }
};
