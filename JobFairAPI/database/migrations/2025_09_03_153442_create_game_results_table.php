<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('game_results', function (Blueprint $table) {
            $table->id();
            $table->string('user_email');
            $table->string('prize_won');
            $table->string('prize_category'); // grand, second, consolation
            $table->string('session_id')->unique();
            $table->boolean('prize_claimed')->default(false);
            $table->timestamp('claimed_at')->nullable();
            $table->json('game_data')->nullable(); // Store wheel position, etc.
            $table->timestamps();
            
            $table->index(['user_email', 'prize_claimed']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_results');
    }
};