<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('reservations');
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
$table->unsignedBigInteger('user_id');
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->enum('session_type', ['job_matching', 'career_talk']);
            $table->dateTime('session_time');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('user_message')->nullable();
            $table->json('additional_info')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'job_id', 'session_time']);
            $table->index(['session_time', 'status']);
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};